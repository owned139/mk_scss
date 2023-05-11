<?php
declare(strict_types=1);
namespace MK\MkScss\Backend\EventListener;

use TYPO3\CMS\Backend\Backend\Event\ModifyClearCacheActionsEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Routing\UriBuilder;

final class CacheActionsEventListener
{
    /**
     * @var string
     */
    protected $key = 'mkscss';

    /**
     * Invoke
     * 
     * @param ModifyClearCacheActionsEvent $event
     * @return void
     */
    public function __invoke(ModifyClearCacheActionsEvent $event): void
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $event->addCacheAction([
            'id' => $this->key,
            'title' => 'LLL:EXT:mk_scss/Resources/Private/Language/locallang.xlf:backend.clear_cache.title',
            'description' => 'LLL:EXT:mk_scss/Resources/Private/Language/locallang.xlf:backend.clear_cache.description',
            'href' => (string)$uriBuilder->buildUriFromRoute('tce_db', ['cacheCmd' => $this->key]),
            'iconIdentifier' => 'mkscss-ext-icon'
        ]);
    }
}