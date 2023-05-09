<?php

use Kiwilan\Opds\Models\OpdsEntry;
use Kiwilan\Opds\Models\OpdsEntryBook;
use Kiwilan\Opds\Models\OpdsEntryBookAuthor;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\Tests\Utils\XmlReader;

it('is string', function () {
    $opds = Opds::response(
        asString: true,
    );

    expect($opds)->toBeString();
});

it('is valid xml', function () {
    $opds = Opds::response(
        asString: true,
    );

    expect(isXMLContentValid($opds))->toBeTrue();
});

it('can be parsed', function () {
    $opds = Opds::response(
        asString: true,
    );

    $xml = XmlReader::toArray($opds);
    expect($xml)->toBeArray();
});

it('can be display entries', function () {
    $opds = Opds::response(
        entries: [
            new OpdsEntry(
                id: 'authors',
                title: 'Authors',
                route: 'http://localhost:8000/opds/authors',
                summary: 'Authors, 1 available',
                media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
                updated: new DateTime(),
            ),
        ],
        asString: true,
    );

    $xml = XmlReader::toArray($opds);
    // dump($xml);
    // expect($xml)->toBeArray();

    expect($opds)->toBeString();
});

it('can be display entries books', function () {
    $opds = Opds::response(
        entries: [
            new OpdsEntryBook(
                id: 'the-clan-of-the-cave-bear-epub-en',
                title: 'The Clan of the Cave Bear',
                route: 'http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en',
                summary: 'The Clan of the Cave Bear is an epic work of prehistoric fiction by Jean M. Auel about prehistoric times. It is the first book in the Earth\'s Children book series which speculates on the possibilities of interactions between Neanderthal and modern Cro-Magnon humans.',
                media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
                updated: new DateTime(),
                download: 'http://localhost:8000/api/download/books/the-clan-of-the-cave-bear-epub-en',
                mediaThumbnail: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
                categories: ['category'],
                authors: [
                    new OpdsEntryBookAuthor(
                        name: 'Jean M. Auel',
                        uri: 'http://localhost:8000/opds/authors/jean-m-auel',
                    ),
                ],
                published: new DateTime(),
                volume: 1,
                serie: 'Earth\'s Children',
                language: 'English',
            ),
        ],
        asString: true,
    );

    // $xml = XmlReader::toArray($opds);
    // expect($xml)->toBeArray();

    expect($opds)->toBeString();
});

it('can search', function () {
    $opds = Opds::response(
        entries: [
            new OpdsEntryBook(
                id: 'the-clan-of-the-cave-bear-epub-en',
                title: 'The Clan of the Cave Bear',
                route: 'http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en',
                summary: 'The Clan of the Cave Bear is an epic work of prehistoric fiction by Jean M. Auel about prehistoric times. It is the first book in the Earth\'s Children book series which speculates on the possibilities of interactions between Neanderthal and modern Cro-Magnon humans.',
                media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
                updated: new DateTime(),
                download: 'http://localhost:8000/api/download/books/the-clan-of-the-cave-bear-epub-en',
                mediaThumbnail: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
                categories: ['category'],
                authors: [
                    new OpdsEntryBookAuthor(
                        name: 'Jean M. Auel',
                        uri: 'http://localhost:8000/opds/authors/jean-m-auel',
                    ),
                ],
                published: new DateTime(),
                volume: 1,
                serie: 'Earth\'s Children',
                language: 'English',
            ),
        ],
        asString: true,
        isSearch: true,
    );

    // $xml = XmlReader::toArray($opds);
    // expect($xml)->toBeArray();

    expect($opds)->toBeString();
});