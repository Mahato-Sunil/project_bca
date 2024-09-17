package com.example.mobilemonitoringsystem

import android.util.Log
import okhttp3.Call
import okhttp3.Callback
import okhttp3.FormBody
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.Response
import java.io.IOException

// class for sending the data to the server
class NetworkManager(private val client: OkHttpClient) {
    fun sendLocationData(
        name: String,
        phone: String,
        ctzn: String,
        latitude: String,
        longitude: String,
        currentTime: String,
        callback: (success: Boolean, message: String) -> Unit
    ) {
        val requestBody = FormBody.Builder().add("user_name", name).add("user_phone", phone)
            .add("user_ctzn", ctzn).add("latitude", latitude).add("longitude", longitude)
            .add("current_time", currentTime).build()

        val request = Request.Builder().url(SERVER_URL).post(requestBody).build()

        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                callback(false, "SMS Message Send... Trying to Update Server...")
                Log.d("NetworkManager", "Failed to send data: ${e.message})")   //for debugging
            }

            override fun onResponse(call: Call, response: Response) {
                val responseBody = response.body?.string() ?: "No response body"
                response.use {
                    if (!response.isSuccessful) {
                        callback(
                            false, "Server Error : We're fixing the errors."
                        )
                        Log.d(
                            "NetworkManager",
                            "Failed to send data: : $responseBody, Code :  ${response.code}"
                        )
                    } else {
                        callback(true, "Emergency Sevices has been Alerted.")
                        Log.d("NetworkManager", "Success : $responseBody")
                    }
                }
            }
        })
    }

    companion object {
        //         private const val SERVER_URL = "http://192.168.1.87/PHP/server-data.php"  //android wifi
        // private const val SERVER_URL = "http://192.168.1.69/PHP/server-data.php"
        // private const val SERVER_URL = "http://192.168.43.85/PHP/server-data.php"  //serv
//         private const val SERVER_URL = "http://192.168.1.231/PHP/server-data.php"  // smc wifi 192.168.1.231
//        private const val SERVER_URL = "http://192.168.137.120/PHP/server-data.php"    // hello kitty
        // private const val SERVER_URL = "http://192.168.1.171/PHP/server-data.php"   // demon
        private const val SERVER_URL = "http://192.168.1.180/PHP/server-data.php"  // 192.168.1.180
    }
}