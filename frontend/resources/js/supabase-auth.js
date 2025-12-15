import { createClient } from "@supabase/supabase-js";

const supabaseUrl = import.meta.env.VITE_SUPABASE_URL;
const supabaseAnonKey = import.meta.env.VITE_SUPABASE_ANON;

if (!supabaseUrl || !supabaseAnonKey) {
    console.error("Missing Supabase environment variables");
}

export const supabase = createClient(supabaseUrl, supabaseAnonKey);

/**
 * Sign in with email and password using Supabase
 * @param {string} email
 * @param {string} password
 */
export async function signInWithSupabase(email, password) {
    const { data, error } = await supabase.auth.signInWithPassword({
        email,
        password,
    });

    if (error) {
        throw new Error(error.message || "Login failed");
    }

    if (data.session && data.session.access_token) {
        // Send token to Laravel backend
        const response = await fetch("/auth/supabase/callback", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: JSON.stringify({
                access_token: data.session.access_token,
            }),
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || "Callback failed");
        }

        const result = await response.json();

        // Redirect based on response
        if (result.redirect) {
            window.location.href = result.redirect;
        }
    }

    return data;
}

/**
 * Sign out from Supabase and Laravel
 */
export async function signOut() {
    try {
        const { error } = await supabase.auth.signOut();
        if (error) throw error;

        window.location.href = "/login";
    } catch (error) {
        console.error("Sign out failed:", error.message);
    }
}
