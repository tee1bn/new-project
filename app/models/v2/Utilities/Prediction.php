<?php

namespace v2\Utilities;

use Illuminate\Database\Capsule\Manager as DB;
use v2\Models\BetcodeConversion;
use v2\Models\LinePrediction;
use Exception;

class Prediction
{
    public $conversion;
    public $found_events;


    public function __construct($conversion)
    {
        $this->conversion = ($conversion);
        return $this;
    }

    public function extractEvents()
    {
        $this->found_events = $this->conversion['home_entries']['found_events'] ?? '';
        return $this;
    }

    public function isAlreadyExtracted()
    {
        return $this->conversion->isExtracted();
    }

    public function hasTranslatedMarket($event)
    {
        if (
            isset($event['translated_prediction']['translated_market']) &&
            isset($event['translated_prediction']['translated_prediction'])
        ) return true;
        return false;
    }

    public function canSaveEvent($event)
    {
        $identifier = $this->getEventIdentifier($event);
        $prediction_by_identifier = LinePrediction::where('identifier', $identifier)->count();
        if ($prediction_by_identifier > 0) return false;
        return true;
    }



    public function saveEvents()
    {
        if (empty($this->found_events) || $this->isAlreadyExtracted()) {
            return false;
        }

        foreach ($this->found_events as $key => $found_event) {
            if (!$this->hasTranslatedMarket($found_event)) continue;

            if ($this->canSaveEvent($found_event)) {
                $this->saveEvent($found_event);
            } else {
                $this->increaseGravity($found_event);
            }
        }

        //mark conversion as extracted
        $home_entries = $this->conversion['home_entries'];
        $home_entries['is_extracted'] = 1;
        $this->conversion->update([
            "home_entries" => json_encode($home_entries)
        ]);
    }

    public function increaseGravity($event)
    {
        $identifier = $this->getEventIdentifier($event);
        $identifier = strtolower($identifier);
        $prediction_by_identifier = LinePrediction::where('identifier', $identifier)->first();
        if (!empty($prediction_by_identifier)) {
            $conversion_ids = explode(',', $prediction_by_identifier->conversion_id);
            $prediction_by_identifier->update(['gravity' => $prediction_by_identifier->gravity + 1]);
            if (!in_array($this->conversion['id'], $conversion_ids)) {
                $prediction_by_identifier->update(['conversion_id' => $prediction_by_identifier->conversion_id . ',' . $this->conversion['id']]);
            }
        }
    }


    public function getEventIdentifier($event)
    {
        $identifier = "{$event['find_code']}/{$event['sport_id']}/{$event['translated_prediction']['translated_market']}/{$event['translated_prediction']['translated_prediction']}";
        $identifier = strtolower($identifier);
        return $identifier;
    }

    public function saveEvent($event)
    {
        DB::beginTransaction();

        $identifier = $this->getEventIdentifier($event);

        try {
            $ad =  LinePrediction::create([
                'found_event' => json_encode($event),
                'conversion_id' => $this->conversion['id'],
                'identifier' => $identifier
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }
    }
}
