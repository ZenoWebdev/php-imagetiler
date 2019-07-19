<?php
/**
 * @filesource   imagetiler.php
 * @created      20.06.2018
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2018 smiley
 * @license      MIT
 */

namespace chillerlan\ImagetilerExamples;

use chillerlan\Imagetiler\{Imagetiler, ImagetilerException, ImagetilerOptions, ImagetilerOptionsTrait};
use chillerlan\Settings\SettingsContainerAbstract;
use ImageOptimizer\OptimizerFactory;
use Psr\Log\AbstractLogger;

require_once __DIR__.'/../vendor/autoload.php';

$input = __DIR__.'/[YOUR HUGE IMAGE].png';
$utils = __DIR__.'/path/to/utils/%s[.exe]';

$options = [
	// ImagetilerOptions
	'zoom_min'             => 0,
	'zoom_max'             => 8,
	'zoom_normalize'       => 6,
	'tms'                  => false,
	'fill_color'           => '#000000',
	'fast_resize'          => false,
	'overwrite_base_image' => false,
	'overwrite_tile_image' => true,
	'clean_up'             => false,
	'optimize_output'      => true,
	'memory_limit'         => '2G',
];

$optimizer_settings = [
	'execute_only_first_png_optimizer' => false,
	'advpng_bin'   => sprintf($utils, 'advpng'),
	'optipng_bin'  => sprintf($utils, 'optipng'),
	'pngcrush_bin' => sprintf($utils, 'pngcrush'),
	'pngquant_bin' => sprintf($utils, 'pngquant'),
];

$options = new ImagetilerOptions($options);

$logger = new class() extends AbstractLogger{
	public function log($level, $message, array $context = []){
		echo sprintf('[%s][%s] %s', date('Y-m-d H:i:s'), substr($level, 0, 4), trim($message))."\n";
	}
};

$optimizer = (new OptimizerFactory($optimizer_settings, $logger))->get();
$tiler     = new Imagetiler($options, $optimizer, $logger);

try{
	$tiler->process($input, __DIR__.'/tiles');
}
catch(ImagetilerException $e){
	$logger->error($e->getMessage());
	$logger->error($e->getTraceAsString());
}
