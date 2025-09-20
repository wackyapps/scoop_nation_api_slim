<?php

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="phone", type="string"),
 *     @OA\Property(property="role", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Address",
 *     type="object",
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="customer_id", type="integer", format="int64"),
 *     @OA\Property(property="address_type", type="string"),
 *     @OA\Property(property="street_address", type="string"),
 *     @OA\Property(property="city", type="string"),
 *     @OA\Property(property="state", type="string"),
 *     @OA\Property(property="postal_code", type="string"),
 *     @OA\Property(property="country", type="string"),
 *     @OA\Property(property="longitude", type="string"),
 *     @OA\Property(property="latitude", type="string"),
 *     @OA\Property(property="is_default", type="boolean")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Profile",
 *     type="object",
 *     @OA\Property(property="fullname", type="string"),
 *     @OA\Property(property="gender", type="string"),
 *     @OA\Property(property="date_of_birth", type="string", format="date"),
 *     @OA\Property(property="phone", type="string"),
 *     @OA\Property(property="email", type="string", format="email")
 * )
 */
