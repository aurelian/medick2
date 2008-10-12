/**
 *
 * It boots a medick application
 *
 * @package medick.core
 */

// application path
define( 'APP_PATH', '<?=$app_real_path;?>' );

// medick framework path.
define('MEDICK_PATH', file_exists(join(DIRECTORY_SEPARATOR, array('<?=$app_real_path;?>', 'vendor', 'medick', 'lib', 'core', 'Medick.php')))? join(DIRECTORY_SEPARATOR, array('<?=$app_real_path;?>', 'vendor', 'medick')): '<?=$medick_path;?>');
// if not frozen:
// define('MEDICK_PATH', '<?=$medick_path;?>');

// rewrite system include path
set_include_path( MEDICK_PATH . DIRECTORY_SEPARATOR . 'lib'   . DIRECTORY_SEPARATOR );

// this should depend on environment
error_reporting( E_ALL | E_STRICT | E_RECOVERABLE_ERROR );

// load core, will setup error_handler and will load other core objects
require 'medick/Medick.php';

Medick::prepare_application();

