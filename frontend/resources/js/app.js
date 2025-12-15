import "../css/app.css";
import { signInWithSupabase, signOut } from "./supabase-auth";

// Export untuk digunakan di blade template
window.signInWithSupabase = signInWithSupabase;
window.signOut = signOut;
