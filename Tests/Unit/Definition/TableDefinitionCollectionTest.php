<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Definition;

use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TableDefinitionCollectionTest extends UnitTestCase
{
    public function twoCollectionsWithTheSameIdentifierRaiseAnExceptionDataProvider(): iterable
    {
        yield 'two collections with the same identifier' => [
            'contentBlocks' => [
                [
                    'composerJson' => [
                        'name' => 't3ce/example',
                    ],
                    'icon' => '',
                    'iconProvider' => '',
                    'yaml' => [
                        'fields' => [
                            [
                                'identifier' => 'foo',
                                'type' => 'Collection',
                                'properties' => [
                                    'fields' => [
                                        [
                                            'identifier' => 'foo',
                                            'type' => 'Collection',
                                            'properties' => [
                                                'fields' => [
                                                    [
                                                        'identifier' => 'text',
                                                        'type' => 'Text'
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider twoCollectionsWithTheSameIdentifierRaiseAnExceptionDataProvider
     * @test
     */
    public function twoCollectionsWithTheSameIdentifierRaiseAnException(array $contentBlocks): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1672449082);
        $this->expectExceptionMessage('A Collection field with the identifier "foo" exists more than once. Please choose another name.');

        TableDefinitionCollection::createFromArray($contentBlocks);
    }

    /**
     * @test
     */
    public function contentElementDefinitionIsFoundByCType(): void
    {
        $contentBlocks = [
            [
                'composerJson' => [
                    'name' => 'foo/bar',
                ],
                'icon' => '',
                'iconProvider' => '',
                'yaml' => [
                    'fields' => []
                ]
            ],
            [
                'composerJson' => [
                    'name' => 't3ce/example',
                ],
                'icon' => '',
                'iconProvider' => '',
                'yaml' => [
                    'fields' => []
                ]
            ],
        ];

        $tableDefinitionCollection = TableDefinitionCollection::createFromArray($contentBlocks);
        $contentElementDefinition = $tableDefinitionCollection->getContentElementDefinition('t3ce_example');

        self::assertNotNull($contentElementDefinition);
        self::assertSame('t3ce', $contentElementDefinition->getVendor());
        self::assertSame('example', $contentElementDefinition->getPackage());
    }

    /**
     * @test
     */
    public function nonExistingContentElementReturnsNull(): void
    {
        $contentBlocks = [
            [
                'composerJson' => [
                    'name' => 't3ce/example',
                ],
                'icon' => '',
                'iconProvider' => '',
                'yaml' => [
                    'fields' => []
                ]
            ]
        ];

        $tableDefinitionCollection = TableDefinitionCollection::createFromArray($contentBlocks);
        $contentElementDefinition = $tableDefinitionCollection->getContentElementDefinition('idonotexist');

        self::assertNull($contentElementDefinition);
    }
}