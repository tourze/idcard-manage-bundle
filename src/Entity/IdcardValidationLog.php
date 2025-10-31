<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\GBT2261\Gender;
use Tourze\IdcardManageBundle\Repository\IdcardValidationLogRepository;

#[ORM\Entity(repositoryClass: IdcardValidationLogRepository::class)]
#[ORM\Table(name: 'idcard_validation_log', options: ['comment' => '身份证验证记录表'])]
class IdcardValidationLog implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;
    use SnowflakeKeyAware;

    public function __toString(): string
    {
        return $this->idcardNumber ?? $this->id ?? '';
    }

    #[Assert\NotBlank(message: '身份证号码不能为空')]
    #[Assert\Length(
        min: 15,
        max: 18,
        minMessage: '身份证号码长度至少15位',
        maxMessage: '身份证号码长度不能超过18位'
    )]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 18, nullable: false, options: ['comment' => '身份证号码'])]
    private ?string $idcardNumber = null;

    #[Assert\NotNull]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['comment' => '是否有效', 'default' => false])]
    private ?bool $isValid = false;

    #[Assert\Date(message: '生日格式不正确，请使用YYYY-MM-DD格式')]
    #[ORM\Column(type: Types::STRING, length: 10, nullable: true, options: ['comment' => '生日（从身份证解析）'])]
    private ?string $birthday = null;

    #[Assert\Choice(callback: [Gender::class, 'cases'], message: '性别值无效')]
    #[ORM\Column(type: Types::INTEGER, nullable: true, enumType: Gender::class, options: ['comment' => '性别（从身份证解析）'])]
    private ?Gender $gender = null;

    #[Assert\Length(max: 100, maxMessage: '验证类型长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '验证类型（如：二元素验证）'])]
    private ?string $validationType = null;

    #[Assert\Json(message: '验证详情必须为有效的JSON格式')]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '验证结果详情（JSON格式）'])]
    private ?string $validationDetails = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?UserInterface $user = null;

    #[Assert\Length(max: 100, maxMessage: '验证来源长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '验证来源'])]
    private ?string $source = null;

    public function getIdcardNumber(): ?string
    {
        return $this->idcardNumber;
    }

    public function setIdcardNumber(?string $idcardNumber): void
    {
        $this->idcardNumber = $idcardNumber;
    }

    public function isValid(): ?bool
    {
        return $this->isValid;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid();
    }

    public function setIsValid(?bool $isValid): void
    {
        $this->isValid = $isValid;
    }

    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    public function setBirthday(?string $birthday): void
    {
        $this->birthday = $birthday;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): void
    {
        $this->gender = $gender;
    }

    public function getValidationType(): ?string
    {
        return $this->validationType;
    }

    public function setValidationType(?string $validationType): void
    {
        $this->validationType = $validationType;
    }

    public function getValidationDetails(): ?string
    {
        return $this->validationDetails;
    }

    public function setValidationDetails(?string $validationDetails): void
    {
        $this->validationDetails = $validationDetails;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): void
    {
        $this->source = $source;
    }
}
