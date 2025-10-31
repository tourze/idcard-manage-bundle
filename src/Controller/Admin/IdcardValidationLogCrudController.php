<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use Tourze\GBT2261\Gender;
use Tourze\IdcardManageBundle\Entity\IdcardValidationLog;
use Tourze\IdcardManageBundle\Enum\GenderDecorator;

/**
 * @extends AbstractCrudController<IdcardValidationLog>
 */
#[AdminCrud(
    routePath: '/idcard-manage/validation-log',
    routeName: 'idcard_manage_validation_log'
)]
final class IdcardValidationLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return IdcardValidationLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('身份证验证记录')
            ->setEntityLabelInPlural('身份证验证记录管理')
            ->setPageTitle(Crud::PAGE_INDEX, '身份证验证记录列表')
            ->setPageTitle(Crud::PAGE_NEW, '新建身份证验证记录')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑身份证验证记录')
            ->setPageTitle(Crud::PAGE_DETAIL, '身份证验证记录详情')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['idcardNumber', 'validationType', 'source'])
            ->showEntityActionsInlined()
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig'])
            ->setPaginatorPageSize(30)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE)
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
        ;

        yield TextField::new('idcardNumber', '身份证号码')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setHelp('请输入15位或18位身份证号码')
        ;

        yield BooleanField::new('isValid', '验证结果')
            ->renderAsSwitch(false)
            ->setColumns('col-md-3')
        ;

        yield TextField::new('validationType', '验证类型')
            ->setColumns('col-md-3')
            ->setHelp('如：基础验证、二元素验证等')
        ;

        yield TextField::new('birthday', '生日')
            ->setColumns('col-md-4')
            ->hideOnIndex()
            ->setHelp('从身份证解析的生日信息')
        ;

        $genderField = EnumField::new('gender', '性别')
            ->setColumns('col-md-4')
        ;
        $genderField->setEnumCases(GenderDecorator::getCasesForEasyAdmin());
        yield $genderField->hideOnIndex();

        yield TextField::new('source', '验证来源')
            ->setColumns('col-md-4')
            ->hideOnIndex()
            ->setHelp('如：前端表单、API接口等')
        ;

        yield TextareaField::new('validationDetails', '验证详情')
            ->setColumns('col-md-12')
            ->onlyOnDetail()
            ->setHelp('JSON格式的详细验证信息')
            ->renderAsHtml(false)
        ;

        yield AssociationField::new('user', '验证用户')
            ->setColumns('col-md-6')
            ->setRequired(false)
            ->hideOnIndex()
        ;

        yield TextField::new('createdFromIp', '创建IP')
            ->onlyOnDetail()
        ;

        yield TextField::new('updatedFromIp', '更新IP')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield AssociationField::new('createdBy', '创建者')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('updatedBy', '更新者')
            ->onlyOnDetail()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('idcardNumber')
            ->add(BooleanFilter::new('isValid'))
            ->add('validationType')
            ->add('gender')
            ->add('source')
            ->add('user')
            ->add(DateTimeFilter::new('createTime'))
            ->add(DateTimeFilter::new('updateTime'))
        ;
    }
}
