package com.example.mobilemonitoringsystem

import android.Manifest
import android.content.Context
import android.content.pm.PackageManager
import android.net.ConnectivityManager
import android.net.NetworkCapabilities
import android.os.Build
import android.os.Bundle
import android.telephony.SmsManager
import android.util.Log
import android.widget.Button
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import okhttp3.OkHttpClient
import java.util.Date

class DataDisplayActivity : AppCompatActivity() {

    private var name = ""
    private var phone = ""
    private var citizenship = ""
    private var currentLat = ""
    private var currentLon = ""
    private var lastLat = ""
    private var lastLon = ""
    private var lastTime = ""
    private var isSmsSent = false

    //global class declaration
    private val isLocation = MyLocation()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_data_display)
        displayTips()
        initializeServer()
    }

    override fun onBackPressed() {
        super.onBackPressed()
        finishAffinity()
    }

    override fun onDestroy() {
        super.onDestroy()
        finishAffinity()
    }

    // Method to check the sms permission
    private fun checkSMSPermission(activity: AppCompatActivity): Boolean {
        return (ContextCompat.checkSelfPermission(
            activity, Manifest.permission.SEND_SMS
        ) == PackageManager.PERMISSION_GRANTED)
    }

    // Method to request the SMS permission if not given
    private fun requestSMSPermission(activity: AppCompatActivity) {
        ActivityCompat.requestPermissions(
            activity, arrayOf(Manifest.permission.SEND_SMS), SMS_PERMISSION_REQUEST_CODE
        )
    }

    //      DISPLAY THE TIPS
    private val tipsArray = arrayOf(
        R.string.tip1,
        R.string.tip2,
        R.string.tip3,
        R.string.tip4,
        R.string.tip5,
        R.string.tip6,
        R.string.tip7,
        R.string.tip8,
        R.string.tip9,
        R.string.tip10
    )

    private lateinit var tipsUi: TextView
    private var currentTipIndex = 0

    private fun displayNextTip() {
        tipsUi.text = getString(tipsArray[currentTipIndex])
        currentTipIndex = (currentTipIndex + 1) % tipsArray.size
    }

    private fun displayTips() {
        //generating the tips
        tipsUi = findViewById(R.id.tipsUi)
        displayNextTip()
        // Launch a Coroutine to display tips periodically
        CoroutineScope(Dispatchers.Main).launch {
            while (true) {
                delay(5000) // 5 seconds delay
                displayNextTip()
            }
        }
    }

    // FUNCTION TO INITIALIZE THE SERVER
    private fun initializeServer() {
        val button: Button = findViewById(R.id.sosBtn)
        val msgUi: TextView = findViewById(R.id.userData)
        button.setOnClickListener {
            //check for the location permission
            if (!isLocation.checkLocationPermission(this)) {
                isLocation.requestPermission(this)
                msgUi.text = getString(
                    R.string.failureMsg,
                    "Location Permission Denied. Please Allow app to use Device Location."
                )
            } else if (!checkSMSPermission(this)) {
                requestSMSPermission(this)
                msgUi.text = getString(
                    R.string.failureMsg,
                    "SMS Permission Denied. Please Allow app to use SMS Function."
                )
            } else sendLocation()
        }
    }

    //function to store the last location to the database
    private fun storeLastLocation(
        lastLat: String, lastLon: String, lastTime: String
    ) {
        // storing the data to the database
        val dbHelper = LastLocationStorage.FeedReaderDbHelper(this)
        dbHelper.insertLastLocationData(
            lastLat, lastLon, lastTime
        )
    }

    // FUNCTION TO GET THE LOCATION DATA AND SEND TO THE SERVER
    private fun sendLocation() {
        //getting the location data from the MyLocation class
        isLocation.getLocation { location ->
            this.currentLat = location.latitude
            this.currentLon = location.longitude
            storeLastLocation(currentLat, currentLon, Date().toString())
            processLocationData()
        }
    }

    // FUNCTION TO PROCESS LOCATION DATA AND SEND IT TO SERVER
    private fun processLocationData() {
        Log.d("Location", "Latitude: $currentLat, Longitude: $currentLon")
        val currentTime = Date().toString()
        val msgUi: TextView = findViewById(R.id.userData)

        // Retrieve data from the database
        val dbHelper = DatabaseActivity.FeedReaderDbHelper(this)
        val data = dbHelper.readLocationData()

        data.forEach {
            name = it.name
            phone = it.phone
            citizenship = it.citizenship
        }

        if (currentLat.isEmpty() || currentLon.isEmpty()) {
            showMessage(
                msgUi,
                getString(R.string.failureMsg, "Try Again: Error Fetching the Current Location.")
            )
        } else {
            if (isInternetAvailable(this)) {
                Toast.makeText(this, "Connected To Internet", Toast.LENGTH_SHORT).show()
                if (!isSmsSent) {
                    sendSmsLocation(currentLat, currentLon, currentTime)
                    isSmsSent = true
                }
                sendLocationToServer(name, phone, citizenship, currentLat, currentLon, currentTime)
                sendLastLocationToServer()
            } else {
                Toast.makeText(this, "No internet connection", Toast.LENGTH_SHORT).show()
            }
        }
    }

    // FUNCTION TO SEND LOCATION TO SERVER
    private fun sendLocationToServer(
        name: String, phone: String, citizenship: String, lat: String, lon: String, time: String
    ) {
        val networkManager = NetworkManager(OkHttpClient())
        networkManager.sendLocationData(
            name, phone, citizenship, lat, lon, time
        ) { success, message ->
            val msgUi: TextView = findViewById(R.id.userData)
            showMessage(msgUi, message)
        }
    }

    // FUNCTION TO SEND LAST LOCATION TO SERVER
    private fun sendLastLocationToServer() {
        val lastDbHelper = LastLocationStorage.FeedReaderDbHelper(this)
        val lastData = lastDbHelper.readLastLocationData()

        lastData.forEach {
            lastLat = it.lat
            lastLon = it.lon
            lastTime = it.time
        }

        if (lastLat.isEmpty() || lastLon.isEmpty() || lastTime.isEmpty()) {
            Toast.makeText(this, "Last Location Unknown...", Toast.LENGTH_SHORT).show()
        } else {
            val lastNetworkManager = NetworkManager(OkHttpClient())
            lastNetworkManager.sendLocationData(
                name, phone, citizenship, lastLat, lastLon, lastTime
            ) { success, message ->
                val msgUi: TextView = findViewById(R.id.userData)
                showMessage(msgUi, message)
            }
        }
    }

    // FUNCTION TO SEND THE LOCATION USING SMS
    private fun sendSmsLocation(curLat: String, curLon: String, curTime: String) {
        // use try catch block for error handling
        try {
            val sms = SmsManager.getDefault()
            val msg =
                "EMERGENCY SOS : \n Latitude : $curLat \n Longitude : $curLon \n Time : $curTime"
            sms.sendTextMessage("+977-9860650642", null, msg, null, null)
            Toast.makeText(this, "SMS Sent!", Toast.LENGTH_SHORT).show()
            Log.d("SMS Manager", msg) // for debugging
        } catch (e: IllegalArgumentException) {
            Toast.makeText(this, "Invalid Phone number!", Toast.LENGTH_SHORT).show()
            Log.d("SMS Manager", "Error:", e)
        } catch (e: Exception) {
            Toast.makeText(this, "SMS Failed to send!", Toast.LENGTH_SHORT).show()
            Log.d("SMS Manager", "Error:", e)
        }
    }

    // FUNCTION TO SHOW MESSAGES
    private fun showMessage(msgUi: TextView, message: String) {
        msgUi.post {
            msgUi.text = message
        }
    }

    // COMPANION OBJECT FOR SMS PERMISSION REQUEST
    companion object {
        private const val SMS_PERMISSION_REQUEST_CODE = 1002
    }

    // FUNCTION TO CHECK THE INTERNET CONNECTION
    private fun isInternetAvailable(context: Context): Boolean {
        val connectivityManager =
            context.getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager

        return if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            val network = connectivityManager.activeNetwork ?: return false
            val activeNetwork = connectivityManager.getNetworkCapabilities(network) ?: return false

            activeNetwork.hasTransport(NetworkCapabilities.TRANSPORT_WIFI) || activeNetwork.hasTransport(
                NetworkCapabilities.TRANSPORT_CELLULAR
            ) || activeNetwork.hasTransport(NetworkCapabilities.TRANSPORT_ETHERNET)
        } else {
            val networkInfo = connectivityManager.activeNetworkInfo ?: return false
            networkInfo.isConnected
        }
    }
}
