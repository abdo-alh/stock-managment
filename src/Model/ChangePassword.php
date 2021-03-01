<?php

namespace App\Model;
 
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
 
class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "كلمة المرور القديمة غير صحيحة"
     * )
     */
    protected $oldPassword;
     
    /**
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "يجب أن تتكون كلمة المرور الخاصة بك من 6 أحرف على الأقل"
     * )
     */
    protected $password;
             
     
    function getOldPassword() {
        return $this->oldPassword;
    }
 
    function getPassword() {
        return $this->password;
    }
 
    function setOldPassword($oldPassword) {
        $this->oldPassword = $oldPassword;
        return $this;
    }
 
    function setPassword($password) {
        $this->password = $password;
        return $this;
    }
}