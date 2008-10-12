// complete path to medick boot.php file.
include_once('<?=make_path( $app_real_path,'boot.php');?>');
// complete path to <?=$app_name;?>.xml and environment to load
$d= new Dispatcher( ContextManager::load(
      '<?=make_path($app_real_path, 'config', $app_name.'.xml');?>',
      'localhost'));
$d->dispatch();

