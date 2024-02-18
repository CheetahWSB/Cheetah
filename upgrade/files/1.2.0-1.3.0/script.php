<?php
    // This script is run at the end of the upgrade before conclusion is shown.
    ch_import('ChWsbModuleDb');
    ch_import('ChWsbInstallerUi');
    ch_import('ChTemplFormView');
    ch_import('ChWsbCacheUtilities');
    
    $oCacheUtilities = new ChWsbCacheUtilities();
    $oModules = new ChWsbModuleDb();
    $oInstallerUi = new ChWsbInstallerUi();
    
    // Clear cache.
    $oCacheUtilities->clear('all');
    
    // Compile languages
    compileLanguage();
    
    // Clear cache.
    $oCacheUtilities->clear('all');
    
    // Compile module languages.
    $aModules = $oModules->getModules();
    $aM = array();
    foreach($aModules as $a) {
        $aM[] = $a['path'];
    }
    $oInstallerUi->_perform($aM, 'recompile');
    
    // Clear cache.
    $oCacheUtilities->clear('all');

    // Recompile forum languages.
    $p = CH_DIRECTORY_PATH_MODULES . 'cheetah/forum/layout/';
    // Removal of the compiled layout folders only needs to be done on
    // this 1.2.0-1.3.0 version upgrade because the compiled Admin.php 
    // script does not currently have the compile override built in.
    // Do not include this removal in the next version upgrade script.
    $d = glob($p . '*_*');
    if($d) {
        foreach($d as $f) {
            deleteDirectory($f);
        }
    }
    $sTime = time();
    file_put_contents(CH_DIRECTORY_PATH_TMP . 'forum_recompile.tmp', $sTime);
    $r = file_get_contents(CH_WSB_URL_ROOT . 'forum/?action=compile_langs&timestamp=' . $sTime);
    
    return true;
