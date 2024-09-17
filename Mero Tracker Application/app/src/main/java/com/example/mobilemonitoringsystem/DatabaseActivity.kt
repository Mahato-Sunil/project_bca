package com.example.mobilemonitoringsystem

import android.content.ContentValues
import android.content.Context
import android.database.sqlite.SQLiteDatabase
import android.database.sqlite.SQLiteOpenHelper
import android.provider.BaseColumns

class DatabaseActivity {

    object FeedReaderContract {
        object FeedEntry : BaseColumns {
            const val TABLE_NAME = "locationData"
            const val COLUMN_NAME = "Name"
            const val COLUMN_PHONE = "Phone"
            const val COLUMN_CTZN = "Citizenship"
        }

        const val SQL_CREATE_TABLE =
            "CREATE TABLE ${FeedEntry.TABLE_NAME} (" + " ${FeedEntry.COLUMN_PHONE} TEXT PRIMARY KEY, " + " ${FeedEntry.COLUMN_NAME} TEXT, " + " ${FeedEntry.COLUMN_CTZN} TEXT)"
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
            const val DATABASE_NAME = "mobileTracker.db"
            const val DATABASE_VERSION = 2
        }

        //        function to insert  the data to the sqlite database
        fun insertLocationData(
            name: String, phone: String, citizenship: String
        ) {

            val db = writableDatabase

            val values = ContentValues().apply {
                put(FeedReaderContract.FeedEntry.COLUMN_NAME, name)
                put(FeedReaderContract.FeedEntry.COLUMN_PHONE, phone)
                put(FeedReaderContract.FeedEntry.COLUMN_CTZN, citizenship)
            }
            db.insert(FeedReaderContract.FeedEntry.TABLE_NAME, null, values)
            db.close()
        }

        // function to read the contents inserted using the database
        // class to store the data retrieved from the database
        data class LocationData(
            val name: String, val phone: String, val citizenship: String
        )

        fun readLocationData(): List<LocationData> {
            val db = readableDatabase
            val projection = arrayOf(
                FeedReaderContract.FeedEntry.COLUMN_NAME,
                FeedReaderContract.FeedEntry.COLUMN_PHONE,
                FeedReaderContract.FeedEntry.COLUMN_CTZN
            )
            //sorting order
            val sortOrder = "${FeedReaderContract.FeedEntry.COLUMN_PHONE} ASC"

            // Query the database
            val cursor = db.query(
                FeedReaderContract.FeedEntry.TABLE_NAME,  // Table to query
                projection,  // The columns to return
                null,  // The columns for the WHERE clause
                null,  // The values for the WHERE clause
                null,  // don't group the rows
                null,  // don't filter by row groups
                sortOrder  // The sort order
            )

            //    store the contents of the database to the  list
            val dataList = mutableListOf<LocationData>()
            with(cursor) {
                while (moveToNext()) {
                    val name =
                        getString(getColumnIndexOrThrow(FeedReaderContract.FeedEntry.COLUMN_NAME))
                    val phone =
                        getString(getColumnIndexOrThrow(FeedReaderContract.FeedEntry.COLUMN_PHONE))
                    val citizenship =
                        getString(getColumnIndexOrThrow(FeedReaderContract.FeedEntry.COLUMN_CTZN))
                    val locationData = LocationData(name, phone, citizenship)
                    dataList.add(locationData)
                }
            }
            cursor.close()
            return dataList
        }
    }
}
