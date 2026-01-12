<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    public function edit()
    {
        $profile = CompanyProfile::get();
        return view('company-profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $profile = CompanyProfile::get();
        
        $profile->fill([
            'company_name' => $validated['company_name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($profile->logo_path && Storage::exists('public/' . $profile->logo_path)) {
                Storage::delete('public/' . $profile->logo_path);
            }

            // Store new logo
            $path = $request->file('logo')->store('company', 'public');
            $profile->logo_path = $path;
        }

        $profile->save();

        return redirect()->route('company-profile.edit')->with('success', 'Profil perusahaan berhasil diperbarui');
    }
}
