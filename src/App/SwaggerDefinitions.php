<?php
namespace App;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Scoop Nation API",
 *         description="API for Scoop Nation E-commerce Platform",
 *         @OA\Contact(
 *             email="wmkhan101@gmail.com"
 *         ),
 *         @OA\License(
 *             name="MIT",
 *             url="https://opensource.org/licenses/MIT"
 *         )
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8080",
 *         description="Local development server"
 *     ),
 *     @OA\Server(
 *         url="https://api.scoopnation.com",
 *         description="Production server"
 *     )
 * )
 * 
 * @OA\Tag(
 *     name="Products",
 *     description="Product management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Bundles",
 *     description="Product bundle management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Users",
 *     description="User management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Customers",
 *     description="Customer management endpoints"
 * )
 * 
 * @OA\Schema(
 *     schema="Error",
 *     type="object",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="error",
 *         type="string",
 *         example="Error message description"
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Vanilla Ice Cream"),
 *     @OA\Property(property="description", type="string", example="Premium vanilla ice cream"),
 *     @OA\Property(property="price", type="number", format="float", example=4.99),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="image_url", type="string", example="/images/vanilla.jpg"),
 *     @OA\Property(property="stock", type="integer", example=100),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Customer",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="firstname", type="string", example="John"),
 *     @OA\Property(property="lastname", type="string", example="Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="company", type="string", example="ACME Corp"),
 *     @OA\Property(property="address", type="string", example="123 Main St"),
 *     @OA\Property(property="apartment", type="string", example="Apt 4B"),
 *     @OA\Property(property="postalCode", type="string", example="10001"),
 *     @OA\Property(property="city", type="string", example="New York"),
 *     @OA\Property(property="country", type="string", example="USA"),
 *     @OA\Property(property="user_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="role", type="string", example="user"),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */
class SwaggerDefinitions
{
    // This class only contains annotations
}