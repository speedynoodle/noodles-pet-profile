<?php
/**
 * API endpoint for quick toiletry logging.
 * Handles POST requests to add pee/poo logs with optional accident flag.
 * 
 * POST parameters:
 *   - token: string (required) - Toiletry access token
 *   - log_type: 'pee' or 'poo' (required)
 *   - is_accident: boolean (optional, default false)
 * 
 * Returns JSON response.
 */

require_once __DIR__ . '/../includes/pet_model.php';

header('Content-Type: application/json');

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    // Get and validate input
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    $logType = filter_input(INPUT_POST, 'log_type', FILTER_SANITIZE_STRING);
    $isAccident = filter_input(INPUT_POST, 'is_accident', FILTER_VALIDATE_BOOLEAN);

    if (!$token || empty($token)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid or missing token']);
        exit;
    }

    if (!in_array($logType, ['pee', 'poo'], true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid log_type. Must be "pee" or "poo"']);
        exit;
    }

    // Verify pet exists by token
    $pet = getPetByToiletryToken($token);
    if (!$pet) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Pet not found']);
        exit;
    }

    $petId = $pet['id'];

    // Save the toiletry log with current NZ time
    $logData = [
        'pet_id'     => $petId,
        'log_type'   => $logType,
        'is_accident'=> $isAccident ? true : false,
        'logged_at'  => getCurrentTimeNZ(),
    ];

    saveToiletryLog($logData);

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Toiletry log saved successfully',
        'log' => [
            'pet_id'     => $petId,
            'log_type'   => $logType,
            'is_accident'=> $logData['is_accident'],
            'logged_at'  => $logData['logged_at'],
            'display_time' => formatDateTimeNZ($logData['logged_at']),
        ]
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    exit;
}

