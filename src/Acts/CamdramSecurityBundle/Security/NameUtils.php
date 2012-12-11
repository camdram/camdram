<?php
namespace Acts\CamdramSecurityBundle\Security;

use Acts\CamdramSecurityBundle\Security\User\SocialUserInterface;

class NameUtils
{

    /**
     * Returns true if we reckon the two names belong to the same person
     * It should be fairly cautious, as the cost of a false positive is greater
     * than that of a false negative.
     *
     * @param $name1
     * @param $name2
     * @return bool
     */
    public function isSamePerson($name1, $name2) {
        /*if ($name1 == $name2) return true;

        //Remove surrounding whitespace and avoid case differences
        $name1 = trim(strtolower($name1));
        $name2 = trim(strtolower($name2));

        $parts1 = preg_split('/[\s,\-]+/',$name1);
        $parts2 = preg_split('/[\s,\-]+/',$name2);

        if (count(array_intersect($parts1, $parts2)) / max(count($parts1), count($parts2)) > 0.5) {
            //i.e. one of them has an extra middle name
            return true;
        }

        //Join back together the parts of each name that are different
        $remain1 = implode(' ', array_diff($parts1, $parts2));
        $remain2 = implode(' ', array_diff($parts2, $parts1));

        $percent = 0;
        similar_text($remain1, $remain2, $percent);

        //Allow a lesser similarity if the first letter is the same
        if (substr($remain1,0,1) == substr($remain2, 0, 1)) {
            if ($percent >= 60) return true;
        }
        if ($percent >= 70) return true;*/

        return $this->getSimilarityScore($name1, $name2) > 70;

        return false;
    }

    public function getSimilarityScore($name1, $name2)
    {
        if ($name1 == $name2) return 100;

        //Remove surrounding whitespace and avoid case differences
        $name1 = trim(strtolower($name1));
        $name2 = trim(strtolower($name2));

        $parts1 = preg_split('/[\s,\-]+/',$name1);
        $parts2 = preg_split('/[\s,\-]+/',$name2);

        $frac_common_parts = count(array_intersect($parts1, $parts2)) / max(count($parts1), count($parts2));
        if ($frac_common_parts > 0.5) {
            //i.e. one of them has an extra middle name
            return (int) round($frac_common_parts * 70) + 30;
        }

        //Join back together the parts of each name that are different
        $remain1 = implode(' ', array_diff($parts1, $parts2));
        $remain2 = implode(' ', array_diff($parts2, $parts1));

        $percent = 0;
        similar_text($remain1, $remain2, $percent);

        //Increase score if the first letter is the same
        if (substr($remain1,0,1) == substr($remain2, 0, 1)) {
            $percent = (int) ($percent * 0.7 + 30);
        }

        return $percent;
    }

    public function filterPossibleUsers($name, array $users)
    {
        $possible = array();

        foreach ($users as $user) {
            if ($this->isSamePerson($name, $user->getName())) {
                $possible [] = $user;
            }
        }

        return $possible;
    }

}
