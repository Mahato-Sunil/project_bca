package com.example.mobilemonitoringsystem

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity

//starting of the main activity
class MainActivity : AppCompatActivity(), View.OnClickListener {

    // method that is run when the app launches
    override fun onCreate(savedInstanceState: Bundle?) {
        //check if the database is empty or not if not empty,  show the registration page
        //checking the database
        val dbHelper = DatabaseActivity.FeedReaderDbHelper(this)
        val ifDataExits = dbHelper.readLocationData()
        if (ifDataExits.isNotEmpty()) {   // move to the next intent or activity
            val intent = Intent(this, DataDisplayActivity::class.java)
            startActivity(intent)
        } else {
            super.onCreate(savedInstanceState)
            setContentView(R.layout.activity_main)
            initializeViews()
        }
    }

    override fun onDestroy() {
        super.onDestroy()
        finishAffinity()
    }
    // method to initialize the  view and declare it
    private fun initializeViews() {
        val button: Button = findViewById(R.id.sendBtn)
        button.setOnClickListener(this)
    }

    // button click event handling
    override fun onClick(view: View?) {
        when (view?.id) {
            R.id.sendBtn -> {
//                disabling the view
                view.isEnabled = false

                //get the text from the text field
                val editName: EditText = findViewById(R.id.userName)
                val editPhone: EditText = findViewById(R.id.phNum)
                val editCtzn: EditText = findViewById(R.id.ctznId)

                val username: String = editName.text.toString()
                val userphone: String = editPhone.text.toString()
                val userctzn: String = editCtzn.text.toString()

                if (username.isEmpty() || userphone.isEmpty() || userctzn.isEmpty()) {
                    Toast.makeText(this, "Please Enter the data !", Toast.LENGTH_SHORT).show()
                    view.isEnabled = true
                } else {
                    storeDatabase(username, userphone, userctzn)
                    val intent = Intent(this, DataDisplayActivity::class.java)
                    startActivity(intent)
                }
            }
        }
    }

    // METHOD TO DISPLAY THE MESSAGE
    private fun storeDatabase(
        username: String, userphone: String, userctzn: String
    ) {
        // storing the data to the databasae
        val dbHelper = DatabaseActivity.FeedReaderDbHelper(this)
        dbHelper.insertLocationData(
            username, userphone, userctzn
        )
    }
}