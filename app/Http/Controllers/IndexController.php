<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller {
    public function index(Request $request) {
        $pages = [
            'privacy-policy' => ['content' => '<b>SummonerNames.com</b> will <i>never</i> share any personal information from any user for any purpose.', 'class' => 'bg-warning'],
            'disclaimer' => ['content' => '<b>SummonerNames.com</b> is not endorsed by Riot Games and does not reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends &copy; Riot Games, Inc.', 'class' => 'bg-warning'],
            'cookie-policy' => ['content' => '<b>SummonerNames.com</b> uses cookies to save the most recent form input submitted. <a href="//support.google.com/adsense/answer/1348695">Additionally, Google Adsense may save some cookies.</a>', 'class' => 'bg-warning'],
            'error' => ['content' => 'error', 'class' => 'bg-clear']
        ];

        $colors = [
            'bg-clear' => 'transparent',
            'bg-primary' => '#337AB7',
            'bg-success' => '#DFF0D8',
            'bg-info' => '#D9EDF7',
            'bg-warning' => '#FCF8E3',
            'bg-danger' => '#F2DEDE'
        ];

        $content = null;
        if (substr($request->path(), 0, 5) == 'page/') {
            $page = substr($request->path(), 5);

            $valid = isset($pages[$page]);

            $content = $pages[$valid ? $page : 'error']['content'];
            $class = $pages[$valid ? $page : 'error']['class'];
            $color = $colors[$class];
        }

        $servers = \App\Servers::all();
        $defaultServer = \App\Servers::getDefault();

        return view('welcome', compact('content', 'page', 'color', 'class', 'servers', 'defaultServer'));
    }
}