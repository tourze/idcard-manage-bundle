<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\IdcardManageBundle\Controller\Admin\IdcardValidationLogCrudController;
use Tourze\IdcardManageBundle\Entity\IdcardValidationLog;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(IdcardValidationLogCrudController::class)]
#[RunTestsInSeparateProcesses]
final class IdcardValidationLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<IdcardValidationLog>
     */
    #[\ReturnTypeWillChange]
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(IdcardValidationLogCrudController::class);
    }

    protected function getEntityFqcn(): string
    {
        return IdcardValidationLog::class;
    }

    public function testControllerCanBeInstantiated(): void
    {
        $controller = new IdcardValidationLogCrudController();
        $this->assertInstanceOf(IdcardValidationLogCrudController::class, $controller);
    }

    public function testConfigureCrudReturnsValidCrudConfig(): void
    {
        $controller = new IdcardValidationLogCrudController();

        // 创建真实的Crud对象来测试
        $crud = Crud::new();
        $result = $controller->configureCrud($crud);

        // 验证返回的是Crud实例
        $this->assertInstanceOf(Crud::class, $result);
    }

    public function testConfigureActionsReturnsValidActionsConfig(): void
    {
        $controller = new IdcardValidationLogCrudController();

        // 创建真实的Actions对象来测试
        $actions = Actions::new();
        $result = $controller->configureActions($actions);

        // 验证返回的是Actions实例
        $this->assertInstanceOf(Actions::class, $result);
    }

    public function testConfigureFieldsReturnsIterableFields(): void
    {
        $controller = new IdcardValidationLogCrudController();
        $fields = $controller->configureFields('index');

        // 将迭代器转换为数组以便测试
        $fieldsArray = iterator_to_array($fields);
        $this->assertNotEmpty($fieldsArray, '字段配置不应为空');

        // 验证字段数量合理
        $this->assertGreaterThan(5, count($fieldsArray), '应该至少有6个字段配置');
    }

    public function testConfigureFiltersReturnsValidFiltersConfig(): void
    {
        $controller = new IdcardValidationLogCrudController();

        // 创建真实的Filters对象来测试
        $filters = Filters::new();
        $result = $controller->configureFilters($filters);

        // 验证返回的是Filters实例
        $this->assertInstanceOf(Filters::class, $result);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'idcard_number' => ['身份证号码'];
        yield 'validation_result' => ['验证结果'];
        yield 'validation_type' => ['验证类型'];
        yield 'create_time' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'idcard_number' => ['idcardNumber'];
        yield 'is_valid' => ['isValid'];
        yield 'validation_type' => ['validationType'];
        yield 'birthday' => ['birthday'];
        yield 'gender' => ['gender'];
        yield 'source' => ['source'];
        yield 'user' => ['user'];
    }

    /**
     * 重写父类方法，验证数据提供器与实际字段配置的一致性
     */

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'idcard_number' => ['idcardNumber'];
        yield 'is_valid' => ['isValid'];
        yield 'validation_type' => ['validationType'];
        yield 'birthday' => ['birthday'];
        yield 'gender' => ['gender'];
        yield 'source' => ['source'];
        yield 'user' => ['user'];
    }

    /**
     * 测试必填字段的验证错误
     */
    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();
        $entityName = $this->getEntitySimpleName();

        // 测试必填字段为空时的验证
        $crawler = $client->request('GET', $this->generateAdminUrl(Action::NEW));
        $this->assertResponseIsSuccessful();

        // 查找表单中的提交按钮
        $submitButtons = $crawler->filter('button[type="submit"], input[type="submit"]');
        if (0 === $submitButtons->count()) {
            self::markTestSkipped('无法找到表单提交按钮');
        }

        $form = $submitButtons->form();

        // 提交空表单以触发验证错误
        $client->submit($form, [
            $entityName => [
                'idcardNumber' => '', // 必填字段为空
                'isValid' => false,
                'validationType' => '',
                'birthday' => '',
                'gender' => '',
                'source' => '',
            ],
        ]);

        // 验证返回422状态码（表单验证失败）
        $this->assertResponseStatusCodeSame(422);

        // 检查是否包含验证错误信息
        $responseContent = $client->getResponse()->getContent();
        $this->assertIsString($responseContent);
        $this->assertStringContainsString('身份证号码不能为空', $responseContent);
    }

    /**
     * 测试有效数据的表单提交
     */
    public function testValidFormSubmission(): void
    {
        $client = $this->createAuthenticatedClient();
        $entityName = $this->getEntitySimpleName();

        $crawler = $client->request('GET', $this->generateAdminUrl(Action::NEW));
        $this->assertResponseIsSuccessful();

        // 查找表单中的提交按钮
        $submitButtons = $crawler->filter('button[type="submit"], input[type="submit"]');
        if (0 === $submitButtons->count()) {
            self::markTestSkipped('无法找到表单提交按钮');
        }

        $form = $submitButtons->form();

        // 提交有效数据
        $client->submit($form, [
            $entityName => [
                'idcardNumber' => '110101199001011234', // 有效的身份证号码
                'isValid' => true,
                'validationType' => '基础验证',
                'birthday' => '1990-01-01',
                'source' => '测试来源',
            ],
        ]);

        // 验证重定向（表单提交成功）
        $this->assertResponseRedirects();
    }

    /**
     * 测试身份证号码长度验证
     */
    public function testIdcardNumberLengthValidation(): void
    {
        $client = $this->createAuthenticatedClient();
        $entityName = $this->getEntitySimpleName();

        $crawler = $client->request('GET', $this->generateAdminUrl(Action::NEW));
        $this->assertResponseIsSuccessful();

        // 查找表单中的提交按钮
        $submitButtons = $crawler->filter('button[type="submit"], input[type="submit"]');
        if (0 === $submitButtons->count()) {
            self::markTestSkipped('无法找到表单提交按钮');
        }

        $form = $submitButtons->form();

        // 测试身份证号码过短
        $client->submit($form, [
            $entityName => [
                'idcardNumber' => '12345', // 长度不足
                'isValid' => false,
                'validationType' => '基础验证',
            ],
        ]);

        // 验证返回422状态码（表单验证失败）
        $this->assertResponseStatusCodeSame(422);
        $responseContent = $client->getResponse()->getContent();
        $this->assertIsString($responseContent);
        $this->assertStringContainsString('身份证号码长度至少15位', $responseContent);
    }
}
