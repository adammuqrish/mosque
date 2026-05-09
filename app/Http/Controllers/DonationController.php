<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    // 1. Paparkan Semua Donasi + Form Masuk
    public function index()
    {
        // Ambil semua donation, ikut tarikh terbaru
        // 'user' adalah relationship yang kita dah define dalam Model Donation tadi
        $donations = Donation::with('user')->latest()->get();

        return view('donations.index', compact('donations'));
    }

    // 2. Simpan Data Baru
    public function store(Request $request)
    {
        // Validasi Data
        $request->validate([
            'amount' => 'required|numeric',
            'category' => 'required|string|max:255',
            'source' => 'required|in:cash,online',
            'donation_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // Simpan ke Database
        Donation::create([
            'user_id' => auth()->id(), // Siapa yang login tu yang masukkan data
            'amount' => $request->amount,
            'category' => $request->category,
            'source' => $request->source,
            'donation_date' => $request->donation_date,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Donation recorded successfully!');
    }
}