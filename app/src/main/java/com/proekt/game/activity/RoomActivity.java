package com.proekt.game.activity;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.app.Activity;
import android.os.Bundle;

import com.proekt.game.MyAdapter;
import com.proekt.game.R;

public class RoomActivity extends Activity {

    private RecyclerView recyclerView;
    private RecyclerView.Adapter mAdapter;
    private RecyclerView.LayoutManager layoutManager;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_room);
        recyclerView = (RecyclerView) findViewById(R.id.users);

        recyclerView.setHasFixedSize(true);

        layoutManager = new LinearLayoutManager(this);
        recyclerView.setLayoutManager(layoutManager);

        String arr[] = new String[10];
        arr[0] = "l";
        arr[1] = "l1";
        mAdapter = new MyAdapter(arr);
        recyclerView.setAdapter(mAdapter);
    }
}
