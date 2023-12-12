<?php

namespace App\Services;

use App\AcfBlocks\AcfBlockInterface;
use App\AcfBlocks\Author;
use App\AcfBlocks\CardHalfAndHalfBlock;
use App\AcfBlocks\CaseStudy;
use App\AcfBlocks\FeatureCardsBlock;
use App\AcfBlocks\GeneralQuote;
use App\AcfBlocks\HowCanWeHelp;
use App\AcfBlocks\ImageFullBlock;
use App\AcfBlocks\ImageLeftTextRightBlock;
use App\AcfBlocks\LatestNewsAndMediaBlock;
use App\AcfBlocks\OurWork;
use App\AcfBlocks\Stats;
use App\AcfBlocks\CallToActionBlock;
use App\AcfBlocks\VideoFullBlock;
use App\AcfBlocks\WysiwygBlock;
use PHPMailer\PHPMailer\Exception;
use WpOrg\Requests\Auth;

/**
 * Register Custom ACF Blocks Here
 * Blocks themselves are defined in app/AcfBlocks
 *
 */
class AcfCustomBlocksService
{

    private $blocks;

    public function __construct()
    {
        // REGISTER  YOUR BLOCKS HERE
        $this->blocks = [
            ImageLeftTextRightBlock::class,
            HowCanWeHelp::class,
            CardHalfAndHalfBlock::class,
            OurWork::class,
            CaseStudy::class,
            GeneralQuote::class,
            Stats::class,
            CallToActionBlock::class,
            Author::class,
            LatestNewsAndMediaBlock::class,
            WysiwygBlock::class,
            ImageFullBlock::class,
            VideoFullBlock::class,
            FeatureCardsBlock::class,
            //... others here
        ];


        try {
            $this->registerBlocks();
        } catch (\Exception $e) {
            // do nothing for now.
        }
    }


    private function registerBlocks(): void
    {
        if (!is_iterable($this->blocks) || empty($this->blocks)) {
            throw new \Exception('No Blocks to register');
        }
        if (!function_exists('acf_register_block')) {
            throw new Exception('ACF Blocks isn\'t available');
        }

        /** @var AcfBlockInterface $block */
        foreach ($this->blocks as $block) {
            $block      = new $block();
            $definition = $block->getBlockDefinition();

            //$definition['render_callback'] = [$block::class, 'acf_block_render_callback'];
            acf_register_block($definition);
        }
    }

}
