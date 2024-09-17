package com.example.mobilemonitoringsystem

import android.content.ContentValues
import android.content.Context
import android.database.sqlite.SQLiteDatabase
import android.database.sqlite.SQLiteOpenHelper
import android.provider.BaseColumns
import android.util.Log

class LastLocationStorage {

    object FeedReaderContract {
        object FeedEntry : BaseColumns {
            const val TABLE_NAME = "tempLocationData"
            const val COLUMN_LATITUDE = "Latitude"
            const val COLUMN_LONGITUDE= "Longitude"
            const val COLUMN_TIME = "Time"
        }

        const val SQL_CREATE_TABLE =
            "CREATE TABLE ${FeedEntry.TABLE_NAME} (" + " ${FeedEntry.COLUMN_LATITUDE} TEXT, " + " ${FeedEntry.COLUMN_LONGITUDE} TEXT, " + " ${FeedEntry.COLUMN_TIME} TEXT)"
        const val SQL_DELETE_TABLE = "DROP TABLE IF EXISTS ${FeedEntry.TABLE_NAME}"
    }

    class FeedReaderDbHelper(context: Context) :
        SQLiteOpenHelper(context, DATABASE_NAME, null, DATABASE_VERSION) {
        override fun onCreate(db: SQLiteDatabase) {
            db.execSQL(FeedReaderContract.SQL_CREATE_TABLE)
        }

        override fun onUpgrade(db: SQLiteDatabase, oldVersion: Int, newVersion: Int) {
            db.execSQL(FeedReaderContract.SQL_DELETE_TABLE)
            onCreate(db)
        }

        override fun onDowngrade(db: SQLiteDatabase, oldVersion: Int, newVersion: Int) {
            onUpgrade(db, oldVersion, newVersion)
        }

        companion object {
            const val DATABASE_NAME = "templocation.db"
            const val DATABASE_VERSION = 1
        }

        //        function to insert  the data to the sqlite database
        fun insertLastLocationData(
            lat: String, lon: String, time: String
        ) {

            val db = writableDatabase

            val values = ContentValues().apply {
                put(FeedReaderContract.FeedEntry.COLUMN_LATITUDE, lat)
                put(FeedReaderContract.FeedEntry.COLUMN_LONGITUDE, lon)
                put(FeedReaderContract.FeedEntry.COLUMN_TIME, time)
            }
            db.insert(FeedReaderContract.FeedEntry.TABLE_NAME, null, values)
            db.close()
        }

        // function to read the contents inserted using the database
        // class to store the data retrieved from the database
        data class LastLocationData(
            val lat: String, val lon: String, val time: String
        )

        fun readLastLocationData(): List<LastLocationData> {
            val db = readableDatabase
            val projection = arrayOf(
                FeedReaderContract.FeedEntry.COLUMN_LATITUDE,
                FeedReaderContract.FeedEntry.COLUMN_LONGITUDE,
                FeedReaderContract.FeedEntry.COLUMN_TIME
            )
            val sortOrder = "${FeedReaderContract.FeedEntry.COLUMN_TIME} DESC"

            val dataList = mutableListOf<LastLocationData>()

            try {
                val cursor = db.query(
                    FeedReaderContract.FeedEntry.TABLE_NAME,  // Table to query
                    projection,  // The columns to return
                    null,  // The columns for the WHERE clause
                    null,  // The values for the WHERE clause
                    null,  // don't group the rows
                    null,  // don't filter by row groups
                    sortOrder  // The sort order
                )

                with(cursor) {
                    while (moveToNext()) {
                        val lat = getString(getColumnIndexOrThrow(FeedReaderContract.FeedEntry.COLUMN_LATITUDE))
                        val lon = getString(getColumnIndexOrThrow(FeedReaderContract.FeedEntry.COLUMN_LONGITUDE))
                        val time = getString(getColumnIndexOrThrow(FeedReaderContract.FeedEntry.COLUMN_TIME))
                        val lastLocationData = LastLocationData(lat, lon, time)
                        dataList.add(lastLocationData)
                    }
                }
                cursor.close()
            } catch (e: Exception) {
                Log.e("DataDisplayActivity", "Error reading last location data", e)
            } finally {
                db.close()
            }

            return dataList
        }

    }
}
