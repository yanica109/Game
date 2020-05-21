package com.proekt.game.activity;
import java.util.HashMap;
import java.util.Map;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.proekt.game.Config;
import com.proekt.game.Controller;
import com.proekt.game.R;
import com.proekt.game.SQLiteHandler;
import com.proekt.game.SessionManager;

import org.json.JSONException;
import org.json.JSONObject;

public class MainActivity extends Activity {
    private static final String TAG = MainActivity.class.getSimpleName();

    private TextView textUsername;
    private Button buttonLogout;
    private Button buttonCreateRoom;

    private ProgressDialog pDialog;
    private SQLiteHandler db;
    private SessionManager session;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        textUsername = (TextView) findViewById(R.id.name);
        buttonLogout = (Button) findViewById(R.id.button_logout);
        buttonCreateRoom = (Button) findViewById(R.id.button_create_room);

        // Progress dialog
        pDialog = new ProgressDialog(this);
        pDialog.setCancelable(false);

        db = new SQLiteHandler(getApplicationContext());

        session = new SessionManager(getApplicationContext());

        if (!session.isLoggedIn()) {
            logoutUser();
        }

        HashMap<String, String> user = db.getUserDetails();

        final String username = user.get("username");

        textUsername.setText(username);

        buttonLogout.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                logoutUser();
            }
        });

        buttonCreateRoom.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                createRoom(username);
            }
        });
    }

    private void logoutUser() {
        session.setLogin(false);

        db.deleteUsers();

        Intent intent = new Intent(MainActivity.this, LoginActivity.class);
        startActivity(intent);
        finish();
    }

    private void createRoom(String username) {
        // Tag used to cancel the request
        String tag_string_req = "req_create_room";
        final String roomName = username + "'s room";

        pDialog.setMessage("Creating room ...");
        showDialog();

        StringRequest strReq = new StringRequest(Request.Method.POST,
                Config.URL_CREATE_ROOM, new Response.Listener<String>() {

            @Override
            public void onResponse(String response) {
                Log.d(TAG, "Create Room Response: " + response.toString());
                hideDialog();

                try {
                    JSONObject jObj = new JSONObject(response);
                    boolean error = jObj.getBoolean("error");
                    if (!error) {

                        JSONObject room = jObj.getJSONObject("room");
                        // Inserting room in rooms table
                        db.addRoom(roomName);

                        Toast.makeText(getApplicationContext(), "Room successfully created!", Toast.LENGTH_LONG).show();

                        // Launch Room activity
                        Intent intent = new Intent(
                                MainActivity.this, //no, room
                                RoomActivity.class);
                        startActivity(intent);
                        finish();
                    } else {



                        String errorMsg = jObj.getString("error_msg");
                        Toast.makeText(getApplicationContext(),
                                errorMsg, Toast.LENGTH_LONG).show();
                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }

            }
        }, new Response.ErrorListener() {

            @Override
            public void onErrorResponse(VolleyError error) {

                Log.e(TAG, "Room Creation Error: " + error.getMessage());
                Toast.makeText(getApplicationContext(),
                        error.getMessage(), Toast.LENGTH_LONG).show();
                hideDialog();
            }
        }) {

            @Override
            protected Map<String, String> getParams() {
                // Posting params to register url
                Map<String, String> params = new HashMap<String, String>();
                params.put("room_name", roomName);

                return params;
            }

        };
        // Adding request to request queue
        Controller.getInstance().addToRequestQueue(strReq, tag_string_req);
    }

    private void showDialog() {
        if (!pDialog.isShowing())
            pDialog.show();
    }

    private void hideDialog() {
        if (pDialog.isShowing())
            pDialog.dismiss();
    }
}