<?php

namespace Kiwilan\Opds;

use Kiwilan\Opds\Models\OpdsApp;
use Kiwilan\Opds\Models\OpdsEntry;
use Kiwilan\Opds\Models\OpdsEntryBook;
use Kiwilan\Opds\Modules\OpdsNotSupportedModule;
use Kiwilan\Opds\Modules\OpdsVersionOneDotTwoModule;

class Opds
{
    public string $url;

    public string $title = 'feed';

    /**
     * @var array<string, mixed>
     */
    public array $urlParts = [];

    /**
     * @var array<string, mixed>
     */
    public array $query = [];

    public string $version = '1.2';

    public OpdsApp $app;

    /**
     * @var OpdsEntry[]|OpdsEntryBook[]
     */
    public array $entries = [];

    /**
     * Create a new instance.
     *
     * @param  string|null  $url Can be null if you want to use the current URL.
     */
    public static function response(
        OpdsApp $app = new OpdsApp(),
        array $entries = [],
        string $title = 'feed',
        ?string $url = null
    ): OpdsResponse {
        $engine = new Opds();

        if ($url) {
            $engine->url = $url;
        } else {
            $engine->url = self::currentUrl();
        }

        $engine->urlParts = parse_url($engine->url);

        if (array_key_exists('query', $engine->urlParts)) {
            parse_str($engine->urlParts['query'], $query);
            $engine->query = $query;

            if (array_key_exists('version', $query)) {
                $engine->version = $query['version'];
            }
        }

        $engine->title = $title;
        $engine->app = $app;
        $engine->entries = $entries;

        return match ($engine->version) {
            '1.2' => OpdsVersionOneDotTwoModule::response($engine),
            default => OpdsNotSupportedModule::response($engine),
        };
    }

    public static function currentUrl(): string
    {
        return (empty($_SERVER['HTTPS']) ? 'http' : 'https')."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
}
