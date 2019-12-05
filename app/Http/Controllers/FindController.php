<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Singletons\RequestsHelper;
use App\Http\Models\FindInPage;
use Carbon\Carbon;
use File;
use DOMXPath;
use DOMDocument;
use PhpParser\Node\Stmt\Foreach_;

class FindController extends Controller
{
    public function FindInPage($city)
    {
        $textToFile = 'Spider run at: ' . Carbon::now('Asia/Jerusalem');
        File::put('C:\xampp\htdocs\Yad2Crawler\public\Log.txt', $textToFile);

        if (!empty($lastRecord = FindInPage::where('city_code', '=', $city['city_code'])->orderBy('id', 'desc')->first()))
        {
            if ($lastRecord)
            {
                $lastRecord = $lastRecord->toArray();
            }
            $lastRecord = $lastRecord['street_name'];
        }
        $pageURL = 'https://www.yad2.co.il/realestate/rent?city='.$city['city_code'].'&price=1000-2000';
        $result = RequestsHelper::getInstance()->doGetRequest($pageURL);
        $page = new DOMDocument;
        libxml_use_internal_errors(true);
        $page->loadHTML($result);
        libxml_clear_errors();
        $xpath = new DOMXPath($page);
        $status[] = $xpath->query('//*[@id="feed_item_0_title"]/text()');
        foreach ($status as $xpathElement) {
            if ($xpathElement->length == 0) {
                $arr[] = 0;
            } else {
                foreach ($xpathElement as $nodeKey => $nodeValue) {
                    $arr[] = $nodeValue->textContent;
                }
            }
        }
        $street_name = preg_replace('/\s+/', '', $arr[0]);

        if (isset($arr) && count($arr) > 0) {
            $findpage = new FindInPage();
            $findpage->street_name = $street_name;
            $findpage->city_code = $city['city_code'];
        }
        if (!(isset($findpage->street_name) && $lastRecord == $street_name))
        {
            $findpage->save();
            print_r("Saved To DB");
            print_r("\n");
            $nexmo = app('Nexmo\Client');
            $nexmo->message()->send([
                'to' => '+972543984604',
                'from' => 'Yad2 Spider',
                'text' => 'New shit on Yad 2.'.' '.$city['city_name'].' '. $pageURL
            ]);
        }
    }
}
