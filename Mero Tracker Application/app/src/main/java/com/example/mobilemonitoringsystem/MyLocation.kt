package com.example.mobilemonitoringsystem

import android.Manifest
import android.content.pm.PackageManager
import android.os.Bundle
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import com.mapbox.common.location.AccuracyLevel
import com.mapbox.common.location.IntervalSettings
import com.mapbox.common.location.LocationObserver
import com.mapbox.common.location.LocationProvider
import com.mapbox.common.location.LocationProviderRequest
import com.mapbox.common.location.LocationService
import com.mapbox.common.location.LocationServiceFactory

class MyLocation : AppCompatActivity() {

    // Define a data class to store the variable
    data class Location(
        var latitude: String,
        var longitude: String
    )

    private val locationPermission = arrayOf(
        Manifest.permission.ACCESS_COARSE_LOCATION, Manifest.permission.ACCESS_FINE_LOCATION
    )

    // Initialize the current latitude and longitude
    private var currentLatitude: Double = 0.0
    private var currentLongitude: Double = 0.0

    private lateinit var locationService: LocationService
    private lateinit var locationObserver: LocationObserver
    private var locationProvider: LocationProvider? = null

    // Method to check the location permission
    fun checkLocationPermission(activity: AppCompatActivity): Boolean {
        return (ContextCompat.checkSelfPermission(
            activity, Manifest.permission.ACCESS_FINE_LOCATION
        ) == PackageManager.PERMISSION_GRANTED && ContextCompat.checkSelfPermission(
            activity, Manifest.permission.ACCESS_COARSE_LOCATION
        ) == PackageManager.PERMISSION_GRANTED)
    }

    // Method to request the location permission if not given
    fun requestPermission(activity: AppCompatActivity) {
        ActivityCompat.requestPermissions(
            activity, locationPermission, LOCATION_PERMISSION_REQUEST_CODE
        )
    }
    // Function to get the location of the user
    // Use of Mapbox to get the location of the user
    fun getLocation(callback: (Location) -> Unit) {
        locationService = LocationServiceFactory.getOrCreate()

        val request = LocationProviderRequest.Builder()
            .interval(
                IntervalSettings.Builder()
                    .interval(1000L) // 1 second interval for updates
                    .minimumInterval(1000L)
                    .maximumInterval(1000L)
                    .build()
            )
            .displacement(0F)
            .accuracy(AccuracyLevel.HIGHEST)
            .build()

        val result = locationService.getDeviceLocationProvider(request)
        if (result.isValue) {
            val locationProvider = result.value!!
            locationObserver = LocationObserver { locations ->
                if (locations.isNotEmpty()) {
                    this.currentLatitude = locations[0].latitude
                    this.currentLongitude = locations[0].longitude
                    val locationData = Location(
                        currentLatitude.toString(),
                        currentLongitude.toString()
                    )
                    runOnUiThread {
                        callback(locationData)
                    }
                }
            }
            locationProvider.addLocationObserver(locationObserver)
        } else {
            Toast.makeText(this, "Sorry Location Access Denied!", Toast.LENGTH_SHORT).show()
        }
    }

    override fun onDestroy() {
        super.onDestroy()
        locationProvider?.removeLocationObserver(locationObserver)
    }

    companion object {
        private const val LOCATION_PERMISSION_REQUEST_CODE = 123
    }
}
