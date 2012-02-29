<?php
Configure::write('Security.salt', 'aOPHmAZVCBjDBSg6bgabIWhAf92QSf6STLd6Z2Se');
Configure::write('Baser.firstAccess', false);
Configure::write('Baser.siteUrl', 'http://localhost/phpcondo/basercms/');
Configure::write('Baser.sslUrl', '');
Configure::write('Baser.adminSslOn', false);
Configure::write('Baser.mobile', false);
Configure::write('Baser.smartphone', false);
Configure::write('Cache.disable', false);
Cache::config('default', array('engine' => 'File'));
Configure::write('App.baseUrl', '');
?>
