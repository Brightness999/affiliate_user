<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function processUpload(Request $request)
    {
        $dublinLat = 53.3340285;
        $dublinLng = -6.2535495;

        if ($request->hasFile('affiliates_file')) {
            $file = $request->file('affiliates_file');
            $affiliatesData = file_get_contents($file->getRealPath());
            $affiliates = explode("\n", $affiliatesData);
            $matchingAffiliates = [];

            foreach ($affiliates as $affiliate) {
                // Extract data for each affiliate
                $affiliateData = json_decode($affiliate, true);
                if ($affiliateData) {
                    $name = $affiliateData['name'];
                    $lat = $affiliateData['latitude'];
                    $lng = $affiliateData['longitude'];
                    $affiliateId = $affiliateData['affiliate_id'];

                    // Calculate distance using the Haversine formula
                    $earthRadiusKm = 6371;
                    $dLat = deg2rad($lat - $dublinLat);
                    $dLng = deg2rad($lng - $dublinLng);
                    $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($dublinLat)) * cos(deg2rad($lat)) * sin($dLng / 2) * sin($dLng / 2);
                    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                    $distanceKm = $earthRadiusKm * $c;

                    // Check if the affiliate is within 100km
                    if ($distanceKm <= 100) {
                        $matchingAffiliates[] = [
                            'name' => $name,
                            'affiliate_id' => $affiliateId,
                            'distance' => number_format(round($distanceKm, 2), 2) . ' km',
                        ];
                    }
                } else {
                    return redirect()->back()->with('error', 'Content is incorrect.');
                }
            }
            // Sort matching affiliates by affiliate_id (ascending)
            usort($matchingAffiliates, function ($a, $b) {
                return $a['affiliate_id'] - $b['affiliate_id'];
            });

            return view('display_affiliates', ['affiliates' => $matchingAffiliates]);
        }

        return redirect()->back()->with('error', 'File not found.');
    }
}
