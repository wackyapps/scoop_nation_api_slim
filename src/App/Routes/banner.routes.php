<?php
declare(strict_types=1);

use App\Controller\BannerController;
use App\Services\EmailService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Get all active banner campaigns for today's date and time with banners and meta
// Accepts an optional X-Branch-Id header to filter branch-specific banners
$app->get('/api/banners/active', [BannerController::class, 'getActiveBannerCampaignsForDateAndTime']);

$app->get('/api/banners', [BannerController::class, 'getAllBanners']);
$app->get('/api/banners/get', [BannerController::class,'getBannerCampaignById']);
$app->post('/api/banners/create', [BannerController::class,'createBannerCampaign']);
$app->post('/api/banners/update', [BannerController::class,'updateBannerCampaign']);
$app->delete('/api/banners/delete', [BannerController::class,'deleteBannerCampaign']);