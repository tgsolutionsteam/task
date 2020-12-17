<?php
namespace Tests\Unit;

use App\Factories\ErrorFactory;
use App\Errors\Messages\PDOExceptionMessages;
use App\Errors\Messages\NoNodesAvailableExceptionMessages;
use Codeception\Test\Unit;
use Phalcon\Http\Response;
use Codeception\Util\HttpCode;
use PDOException;
use Exception;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;

class ErrorFactoryTest extends Unit
{

    private $response;
    private $exValue;

    public function setUp(): void
    {
        parent::setUp();

        $this->exValue = 'StudentTest';
        $this->response = new Response();
    }

    public function testErrorMessagePdoDuplicateEntryException(): void
    {
        $pdoViolation = 23000;
        $pdoError = 1062;
        $ex = new PDOException(
            "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '{$this->exValue}' for key 'idx_un'",
            $pdoViolation
        );
        $ex->errorInfo = ['23000', $pdoError, "Duplicate entry '{$this->exValue}' for key 'idx_unq'"];

        $errorFactory = new ErrorFactory($this->response, $ex);
        $errorFactory->buildErrorMessage();
        $this->response->send();

        $message = str_replace(
            "{{value}}",
            "'{$this->exValue}'",
            (new PDOExceptionMessages)->getMessages()[$pdoViolation][$pdoError]
        );

        $expectedError = ['message' => $message];

        $this->assertEquals($this->response->getContent(), json_encode($expectedError));
        $this->assertEquals(HttpCode::CONFLICT, $this->response->getStatusCode());
    }

    public function testErrorMessageDefaultException(): void
    {
        $errorMsg = "Default exception error";
        $ex = new Exception($errorMsg);

        $errorFactory = new ErrorFactory($this->response, $ex);
        $errorFactory->buildErrorMessage();
        $this->response->send();

        $expectedError = ['message' => $errorMsg];

        $this->assertEquals($this->response->getContent(), json_encode($expectedError));
        $this->assertEquals(HttpCode::CONFLICT, $this->response->getStatusCode());
    }
}
