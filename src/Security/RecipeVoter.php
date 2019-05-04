<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Recipe;
use App\Entity\User;
use App\Service\RecipeService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class RecipeVoter extends Voter
{
    const RECIPE_EDIT = 'RECIPE_EDIT';

    /**
     * @var RecipeService
     */
    private $recipeService;

    /**
     * @var Security
     */
    private $security;

    /**
     * AccountVoter constructor.
     *
     * @param RecipeService $recipeService
     * @param \Security $security
     */
    public function __construct(
        RecipeService $recipeService,
        Security $security
    ) {
        $this->recipeService = $recipeService;
        $this->security = $security;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, [
                self::RECIPE_EDIT,
            ])) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $slug = $subject;

        switch ($attribute) {
            case self::RECIPE_EDIT:
                return $this->canEdit($user, $slug);
        }

        throw new \LogicException("This code should not be reached!");
    }

    /**
     * @param User $user
     * @param string $slug
     *
     * @return bool
     */
    private function canEdit(User $user, string $slug): bool
    {
        $recipe = $this->recipeService->getRecipeBySlug($slug);

        if ($recipe instanceof Recipe) {
            if ($recipe->getUser()->getId() === $user->getId() && $this->security->isGranted("ROLE_ADMIN")) {
                return true;
            }
        }

        return false;
    }
}
