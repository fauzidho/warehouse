import { initializeApp } from "firebase/app";
import { getFirestore } from "firebase/firestore";
import { getDatabase } from "firebase/database";

// Firebase Configuration Toko Kopi
export const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY || "AIzaSyDSfrl6WclAYdSoPtxsFt1Pvh5N2NReqMw",
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN || "toko-kopi-8abbd.firebaseapp.com",
    databaseURL: import.meta.env.VITE_FIREBASE_DATABASE_URL || "https://toko-kopi-8abbd-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || "toko-kopi-8abbd",
    storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET || "toko-kopi-8abbd.firebasestorage.app",
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID || "1076301234300",
    appId: import.meta.env.VITE_FIREBASE_APP_ID || "1:1076301234300:web:8fc39d721282f5ec70c9f9"
};

// Initialize Firebase App
export const app = initializeApp(firebaseConfig);

// Initialize Cloud Firestore & Realtime Database
export const db = getFirestore(app);
export const rtdb = getDatabase(app);

// Expose to window for global access across Blade templates
if (typeof window !== "undefined") {
    window.firebaseApp = app;
    window.firestoreDb = db;
    window.realtimeDb = rtdb;
    console.log("🔥 Firebase initialized successfully for toko-kopi-8abbd");
}

export default app;
