<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

enum ExceptionType: string implements EnumLabel
{
    case unknown = 'unknown';

    case notFound = 'not_found';

    case badRequest = 'bad_request';

    case accessDenied = 'access_denied';

    case methodNotAllowed = 'method_not_allowed';

    case tooManyRequests = 'too_many_requests';

    case unauthorized = 'unauthorized';

    case validationFields = 'validation_fields';

    case invalidToken = 'invalid_token';

    case tokenNotFound = 'token_not_found';
    case payment = 'payment';

    public function label(): string
    {
        return match ($this) {
            self::unknown => 'Unknown error',
            self::notFound => 'Not found',
            self::badRequest => 'Bad request',
            self::accessDenied => 'Access Denied',
            self::methodNotAllowed => 'Method not allowed',
            self::tooManyRequests => 'Too many requests',
            self::unauthorized => 'Unauthorized',
            self::validationFields => 'Validation Error',
            self::invalidToken => 'Invalid token',
            self::tokenNotFound => 'Token not found',
            self::payment => 'Payment error',
        };
    }
}
