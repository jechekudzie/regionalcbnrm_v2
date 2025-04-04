<?php

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Resource Africa API Documentation",
 *      description="API Documentation for Resource Africa mobile application",
 *      @OA\Contact(
 *          email="admin@resourceafrica.org"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="API endpoints for user authentication"
 * )
 *
 * @OA\Tag(
 *     name="Organizations",
 *     description="API endpoints for organizations"
 * )
 *
 * @OA\Tag(
 *     name="Wildlife Conflicts",
 *     description="API endpoints for wildlife conflict incidents"
 * )
 *
 * @OA\Tag(
 *     name="Problem Animal Control",
 *     description="API endpoints for problem animal control"
 * )
 *
 * @OA\Tag(
 *     name="Poaching",
 *     description="API endpoints for poaching incidents"
 * )
 *
 * @OA\Tag(
 *     name="Hunting",
 *     description="API endpoints for hunting activities"
 * )
 *
 * @OA\Tag(
 *     name="Species",
 *     description="API endpoints for species"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
