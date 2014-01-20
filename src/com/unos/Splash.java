package com.unos;

import com.unos.R;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;

public class Splash extends Activity implements Runnable {

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.splash);

		Handler handler = new Handler();
		handler.postDelayed(this, 18000);
	}

	public void run(){
		//inicia a segunda a tividade deppois do splash
		startActivity(new Intent(this, MainActivity.class));
		finish();
	}
}