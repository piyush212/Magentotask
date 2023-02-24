<?php

namespace Codilar\SocialLogin\Api\Data;

interface SocialLoginDataInterfaceInterface
{
    /**
     * String constants for property names
     */
    const ID = "id";
    const EMAIL = "email";
    const FIRSTNAME = "firstname";
    const LASTNAME = "lastname";
    const SOCIAL_TOKEN = "social_token";
    /**
     * Getter for Id.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Setter for Id.
     *
     * @param int|null $id
     *
     * @return void
     */
    public function setId(?int $id): void;

    /**
     * Getter for Email.
     *
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * Setter for Email.
     *
     * @param string|null $email
     *
     * @return void
     */
    public function setEmail(?string $email): void;

    /**
     * Getter for Firstname.
     *
     * @return string|null
     */
    public function getFirstname(): ?string;

    /**
     * Setter for Firstname.
     *
     * @param string|null $firstname
     *
     * @return void
     */
    public function setFirstname(?string $firstname): void;

    /**
     * Getter for Lastname.
     *
     * @return string|null
     */
    public function getLastname(): ?string;

    /**
     * Setter for Lastname.
     *
     * @param string|null $lastname
     *
     * @return void
     */
    public function setLastname(?string $lastname): void;

    /**
     * Getter for SocialToken.
     *
     * @return string|null
     */
    public function setSocialToken(?string $socialToken): void;

    /**
     * Setter for SocialToken.
     *
     * @return string|null
     */
    public function getSocialToken(): ?string;
}
