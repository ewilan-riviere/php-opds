<?php

namespace Kiwilan\Opds;

use Kiwilan\Opds\Engine\OpdsEngine;
use Kiwilan\Opds\Engine\OpdsJsonEngine;
use Kiwilan\Opds\Engine\OpdsXmlEngine;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Enums\OpdsOutputEnum;
use Kiwilan\Opds\Enums\OpdsVersionEnum;

class Opds
{
    /**
     * @param  array<string, mixed>  $urlParts
     * @param  array<string, mixed>  $query
     * @param  OpdsEntryNavigation[]|OpdsEntryBook[]  $feeds
     */
    protected function __construct(
        protected OpdsConfig $config = new OpdsConfig(),
        protected ?string $url = null,
        protected string $title = 'feed',
        protected OpdsVersionEnum $version = OpdsVersionEnum::v1Dot2,
        protected ?OpdsVersionEnum $queryVersion = null,
        protected array $urlParts = [],
        protected array $query = [],
        protected array $feeds = [],
        protected bool $isSearch = false,
        protected ?OpdsEngine $engine = null,
        protected ?OpdsOutputEnum $output = null, // xml or json
        protected ?OpdsResponse $response = null,
    ) {
    }

    /**
     * Create a new instance.
     */
    public static function make(OpdsConfig $config = new OpdsConfig()): self
    {
        $self = new self($config);

        $self->url = OpdsEngine::getCurrentUrl();
        $self->parseUrl();

        if ($config->isForceJson()) {
            $self->version = OpdsVersionEnum::v2Dot0;
        }

        if ($self->queryVersion) {
            $self->version = $self->queryVersion;
        }

        return $self;
    }

    /**
     * Title of current OPDS, default is `OPDS`.
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Default URL is built from `$_SERVER['HTTP_HOST']` and `$_SERVER['REQUEST_URI']`, but in some cases you may want to override this.
     */
    public function url(string $url): self
    {
        $this->url = $url;
        $this->parseUrl();

        return $this;
    }

    /**
     * Navigation Feeds or Acquisition Feeds.
     *
     * @param  OpdsEntryNavigation[]|OpdsEntryBook[]|OpdsEntryNavigation|OpdsEntryBook  $feeds
     */
    public function feeds(mixed $feeds): self
    {
        if (! is_array($feeds)) {
            $feeds = [$feeds];
        }

        $this->feeds = $feeds;

        return $this;
    }

    public function isSearch(): self
    {
        $this->isSearch = true;

        return $this;
    }

    /**
     * Get OPDS with full response, you generate response with headers with `response()`.
     */
    public function get(): self
    {
        if ($this->queryVersion) {
            $this->version = $this->queryVersion;
        }

        if ($this->config->isForceJson()) {
            $this->version = OpdsVersionEnum::v2Dot0;
        }

        if ($this->version === OpdsVersionEnum::v1Dot2) {
            $this->output = OpdsOutputEnum::xml;
        }

        if ($this->version === OpdsVersionEnum::v2Dot0) {
            $this->output = OpdsOutputEnum::json;
        }

        $this->engine = match ($this->version) {
            OpdsVersionEnum::v1Dot2 => OpdsXmlEngine::make($this),
            OpdsVersionEnum::v2Dot0 => OpdsJsonEngine::make($this),
        };
        $this->response = OpdsResponse::make($this->engine, 200);

        return $this;
    }

    /**
     * Send response to browser.
     *
     * @param  bool  $send  To send valid response to browser it should be to `true`.
     * @return  void|never
     */
    public function response(bool $send = true)
    {
        if (! $this->response) {
            $this->get();
        }

        $this->response->response($send);
    }

    /**
     * Parse URL.
     */
    private function parseUrl(): self
    {
        $this->urlParts = parse_url($this->url);
        $query = $this->urlParts['query'] ?? null;

        if (! $query) {
            return $this;
        }

        parse_str($this->urlParts['query'], $query);
        $this->query = $query;

        $version = $query[$this->config->getVersionQuery()] ?? null;

        if (! $version) {
            return $this;
        }

        $enumVersion = match ($version) {
            '1.2' => OpdsVersionEnum::v1Dot2,
            '2.0' => OpdsVersionEnum::v2Dot0,
            default => null,
        };

        if ($version !== null && $enumVersion === null) {
            throw new \Exception("OPDS version {$version} is not supported.");
        }

        if ($enumVersion) {
            $this->queryVersion = $enumVersion;
        }

        return $this;
    }

    /**
     * Get current URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get OPDS version.
     */
    public function getVersion(): OpdsVersionEnum
    {
        return $this->version;
    }

    /**
     * Get OPDS configuration.
     */
    public function getConfig(): OpdsConfig
    {
        return $this->config;
    }

    /**
     * Get URL parts.
     */
    public function getUrlParts(): array
    {
        return $this->urlParts;
    }

    /**
     * Get query.
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Get feeds.
     *
     * @return  OpdsEntryNavigation[]|OpdsEntryBook[]
     */
    public function getFeeds(): array
    {
        return $this->feeds;
    }

    /**
     * Know if current page is search page.
     */
    public function checkIfSearch(): bool
    {
        return $this->isSearch;
    }

    /**
     * Get OPDS engine.
     */
    public function getEngine(): ?OpdsEngine
    {
        if (! $this->engine) {
            $this->get();
        }

        return $this->engine;
    }

    /**
     * Get OPDS output: xml or json.
     */
    public function getOutput(): ?OpdsOutputEnum
    {
        if (! $this->output) {
            $this->get();
        }

        return $this->output;
    }

    /**
     * Get OPDS response.
     */
    public function getResponse(): ?OpdsResponse
    {
        if (! $this->response) {
            $this->get();
        }

        return $this->response;
    }
}
