<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\GBT2261\Gender;
use Tourze\IdcardManageBundle\Entity\IdcardValidationLog;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(IdcardValidationLog::class)]
final class IdcardValidationLogTest extends AbstractEntityTestCase
{
    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'idcardNumber' => ['idcardNumber', '110101199001011234'];
        yield 'isValid' => ['isValid', true];
        yield 'birthday' => ['birthday', '1990-01-01'];
        yield 'gender' => ['gender', Gender::MAN];
        yield 'validationType' => ['validationType', '二元素验证'];
        yield 'validationDetails' => ['validationDetails', '{"result": "valid", "score": 95}'];
        yield 'source' => ['source', 'API接口'];
    }

    protected function createEntity(): object
    {
        return new IdcardValidationLog();
    }

    public function testCanCreateIdcardValidationLog(): void
    {
        $log = new IdcardValidationLog();
        $this->assertInstanceOf(IdcardValidationLog::class, $log);
    }

    public function testCanSetAndGetIdcardNumber(): void
    {
        $log = new IdcardValidationLog();
        $idcardNumber = '110101199001011234';

        $log->setIdcardNumber($idcardNumber);
        $this->assertSame($idcardNumber, $log->getIdcardNumber());
    }

    public function testCanSetAndGetIsValid(): void
    {
        $log = new IdcardValidationLog();

        $log->setIsValid(true);
        $this->assertTrue($log->isValid());

        $log->setIsValid(false);
        $this->assertFalse($log->isValid());
    }

    public function testCanSetAndGetBirthday(): void
    {
        $log = new IdcardValidationLog();
        $birthday = '1990-01-01';

        $log->setBirthday($birthday);
        $this->assertSame($birthday, $log->getBirthday());
    }

    public function testCanSetAndGetGender(): void
    {
        $log = new IdcardValidationLog();

        $log->setGender(Gender::MAN);
        $this->assertSame(Gender::MAN, $log->getGender());

        $log->setGender(Gender::WOMAN);
        $this->assertSame(Gender::WOMAN, $log->getGender());

        $log->setGender(Gender::UNKNOWN);
        $this->assertSame(Gender::UNKNOWN, $log->getGender());
    }

    public function testCanSetAndGetValidationType(): void
    {
        $log = new IdcardValidationLog();
        $validationType = '二元素验证';

        $log->setValidationType($validationType);
        $this->assertSame($validationType, $log->getValidationType());
    }

    public function testCanSetAndGetValidationDetails(): void
    {
        $log = new IdcardValidationLog();
        $details = '{"result": "valid", "score": 95}';

        $log->setValidationDetails($details);
        $this->assertSame($details, $log->getValidationDetails());
    }

    public function testCanSetAndGetSource(): void
    {
        $log = new IdcardValidationLog();
        $source = 'API接口';

        $log->setSource($source);
        $this->assertSame($source, $log->getSource());
    }

    public function testToStringReturnsIdcardNumber(): void
    {
        $log = new IdcardValidationLog();
        $idcardNumber = '110101199001011234';

        $log->setIdcardNumber($idcardNumber);
        $this->assertSame($idcardNumber, (string) $log);
    }

    public function testToStringReturnsIdWhenNoIdcardNumber(): void
    {
        $log = new IdcardValidationLog();

        // 由于我们不能直接设置ID（它是由Doctrine管理的），
        // 这个测试主要确保toString方法不会出错
        $result = (string) $log;
        // 当id和idcardNumber都为null时应该返回空字符串
        $this->assertSame('', $result, '__toString() 应该返回空字符串当ID和身份证号都为空时');
    }
}
