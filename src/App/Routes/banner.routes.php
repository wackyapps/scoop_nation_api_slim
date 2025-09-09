<?php
declare(strict_types=1);

use App\Controller\BannerController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Get all active banner campaigns for today's date and time with banners and meta
$app->get('/api/banners/active', [BannerController::class, 'getActiveBannerCampaignsForDateAndTime']);