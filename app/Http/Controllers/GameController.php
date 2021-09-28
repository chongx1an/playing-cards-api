<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGameRequest;
use Exception;
use Illuminate\Http\Request;

class GameController extends Controller
{

    public function create(CreateGameRequest $request)
    {
        $playersCount = $request->player_count;

        # Initialise cards
        $suits = ["S", "H", "D", "C"];
        $ranks = collect(["A", range(2, 9), "X", "J", "Q", "K"])->flatten();

        $cards = collect($suits)->map(function ($suit) use ($ranks) {
            return collect($ranks)->map(function ($rank) use ($suit) {
                return "$suit$rank";
            });
        })->flatten();


        # Suffle cards
        $cards = $cards->shuffle()->toArray();


        # Distribute cards
        $playersCards = [];
        while (!empty($cards)) {
            foreach (range(1, $playersCount) as $player) {
                $playersCards[$player][] = array_shift($cards);
            }
        }


        # Format output
        $result = collect($playersCards)
            ->map(function ($playerCards) {
                return implode(",", array_filter($playerCards));
            })
            ->toArray();

        $result = implode("\r", $result);

        return response()->json([
            "cards" => $result
        ]);
    }
}
