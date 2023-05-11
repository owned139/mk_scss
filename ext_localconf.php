<?php
defined('TYPO3') or die();

call_user_func(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = 'MK\MkScss\Hooks\PageRendererHook->RenderPreProcess';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = 'MK\MkScss\Hooks\CacheHook->clearCachePostProc';
});
