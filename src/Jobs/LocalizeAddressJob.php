<?php

namespace Enflow\Address\Jobs;

use Enflow\Address\DriverManager;
use Enflow\Address\Models\Address;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\SerializesModels;

class LocalizeAddressJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels, Queueable;

    // https://github.com/therezor/laravel-transactional-jobs/pull/7
    // public $afterTransactions = true;

    private Address $address;
    private string $language;
    public $deleteWhenMissingModels = true;

    public function __construct(Address $address, string $language)
    {
        $this->address = $address;
        $this->language = $language;
    }

    public function handle(DriverManager $driverManager)
    {
        $translated = $driverManager->driver($this->address->driver)->lookup($this->address->identifier, [
            'language' => $this->language,
        ]);

        foreach ($this->address->getTranslatableAttributes() as $attribute) {
            $this->address->setTranslation($attribute, $this->language, $translated->getTranslationWithoutFallback($attribute, $this->language));
        }
        $this->address->save();
    }
}
