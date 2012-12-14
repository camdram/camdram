<?php

namespace Application\Migrations;

use Zenstruck\Bundle\MigrationsBundle\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Acts\CamdramSecurityBundle\Entity\UserIdentity;
use Acts\CamdramSecurityBundle\Entity\Group;
use Acts\CamdramBundle\Entity\NameAlias;
use Acts\CamdramBundle\Entity\Person;
use Doctrine\ORM\EntityManager;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121213041940 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("CREATE TABLE acts_user_identities (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, service VARCHAR(50) NOT NULL, remote_id VARCHAR(100) DEFAULT NULL, remote_user VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, token_secret VARCHAR(255) DEFAULT NULL, INDEX IDX_B4BCDC47A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_user_group_links (group_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C789B1F6FE54D947 (group_id), INDEX IDX_C789B1F6A76ED395 (user_id), PRIMARY KEY(group_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_classes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_type VARCHAR(200) NOT NULL, UNIQUE INDEX UNIQ_69DD750638A36066 (class_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_security_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, identifier VARCHAR(200) NOT NULL, username TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8835EE78772E836AF85E0677 (identifier, username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_object_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_object_identity_id INT UNSIGNED DEFAULT NULL, class_id INT UNSIGNED NOT NULL, object_identifier VARCHAR(100) NOT NULL, entries_inheriting TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_9407E5494B12AD6EA000B10 (object_identifier, class_id), INDEX IDX_9407E54977FA751A (parent_object_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_object_identity_ancestors (object_identity_id INT UNSIGNED NOT NULL, ancestor_id INT UNSIGNED NOT NULL, INDEX IDX_825DE2993D9AB4A6 (object_identity_id), INDEX IDX_825DE299C671CEA1 (ancestor_id), PRIMARY KEY(object_identity_id, ancestor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_entries (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_id INT UNSIGNED NOT NULL, object_identity_id INT UNSIGNED DEFAULT NULL, security_identity_id INT UNSIGNED NOT NULL, field_name VARCHAR(50) DEFAULT NULL, ace_order SMALLINT UNSIGNED NOT NULL, mask INT NOT NULL, granting TINYINT(1) NOT NULL, granting_strategy VARCHAR(30) NOT NULL, audit_success TINYINT(1) NOT NULL, audit_failure TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_46C8B806EA000B103D9AB4A64DEF17BCE4289BF4 (class_id, object_identity_id, field_name, ace_order), INDEX IDX_46C8B806EA000B103D9AB4A6DF9183C9 (class_id, object_identity_id, security_identity_id), INDEX IDX_46C8B806EA000B10 (class_id), INDEX IDX_46C8B8063D9AB4A6 (object_identity_id), INDEX IDX_46C8B806DF9183C9 (security_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_name_aliases (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

        $this->addSql("ALTER TABLE acts_user_identities ADD CONSTRAINT FK_B4BCDC47A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_user_group_links ADD CONSTRAINT FK_C789B1F6FE54D947 FOREIGN KEY (group_id) REFERENCES acts_groups (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_user_group_links ADD CONSTRAINT FK_C789B1F6A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_object_identities ADD CONSTRAINT FK_9407E54977FA751A FOREIGN KEY (parent_object_identity_id) REFERENCES acl_object_identities (id)");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE2993D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE299C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806EA000B10 FOREIGN KEY (class_id) REFERENCES acl_classes (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B8063D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806DF9183C9 FOREIGN KEY (security_identity_id) REFERENCES acl_security_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");

        $this->addSql("ALTER TABLE `acts_events` CHANGE `socid` socid INT DEFAULT NULL");
        $this->addSql("ALTER TABLE `acts_users` ADD person_id INT DEFAULT NULL, ADD upgraded_at DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_users ADD CONSTRAINT FK_62A20753217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)");
        $this->addSql("CREATE INDEX IDX_62A20753217BBB47 ON acts_users (person_id)");

        $this->addSql("ALTER TABLE acts_name_aliases ADD CONSTRAINT FK_355DA778217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)");
        $this->addSql("CREATE INDEX IDX_355DA778217BBB47 ON acts_name_aliases (person_id)");
        $this->addSql("ALTER TABLE `acts_shows` CHANGE venid venid INT DEFAULT NULL, CHANGE socid socid INT DEFAULT NULL");
        $this->addSql("UPDATE acts_shows AS s LEFT JOIN acts_venues AS v ON  s.venid = v.id SET s.venid = NULL WHERE v.id IS NULL");
        $this->addSql("UPDATE acts_shows AS s LEFT JOIN acts_societies_new AS c ON  s.socid = c.id SET s.socid = NULL WHERE c.id IS NULL");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE40A73EBA FOREIGN KEY (venid) REFERENCES acts_venues (id)");
        $this->addSql("CREATE INDEX IDX_1A1A53FE40A73EBA ON acts_shows (venid)");
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6");
        $this->addSql("UPDATE acts_performances AS p LEFT JOIN acts_venues AS v ON  p.venid = v.id SET p.venid = NULL WHERE v.id IS NULL");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venid) REFERENCES acts_venues (id)");
        $this->addSql("UPDATE acts_events AS e LEFT JOIN acts_societies_new AS c ON  e.socid = c.id SET e.socid = NULL WHERE c.id IS NULL");
        $this->addSql("ALTER TABLE acts_events ADD CONSTRAINT FK_78452C7AAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies_new (id)");
        $this->addSql("UPDATE acts_applications AS a LEFT JOIN acts_societies_new AS c ON  a.socid = c.id SET a.socid = NULL WHERE c.id IS NULL");
        $this->addSql("ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0FAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies_new (id)");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies_new (id)");

        $this->addSql('SET foreign_key_checks = 0');
    }

    public function dataUp(ContainerInterface $container)
    {
        $em = $container->get('doctrine.orm.entity_manager');
        $em->getConnection()->exec('SET foreign_key_checks = 0');
        $utils = $container->get('camdram.security.name_utils');

        $this->removeRedundantPeople($em);
        $this->createGroupsFromSocieties($em);
        $this->generateIdentities($em);
        $this->matchPeopleToUsers($em);
        $this->mergeMapToPeople($em);
        $this->matchPeople($em, $utils);
        $em->getConnection()->exec('SET foreign_key_checks = 1');
    }

    public function removeRedundantPeople(EntityManager $em)
    {
        $people_res = $em->getRepository('ActsCamdramBundle:Person');
        $query = $people_res->createQueryBuilder('p')
            ->leftJoin('ActsCamdramBundle:Role', 'r')
            ->where('r.id is null')
            ->getQuery();
        $people = $query->getResult();
        foreach ($people as $p) {
            $em->remove($p);
            echo 'Deleted '.$p->getName()."\r\n";
        }
        $em->flush();
    }

    /**
     * Where the 'map_to' field has been used, simply flatten into a single person
     */
    public function matchPeople(EntityManager $em, $name_utils)
    {
        $people_res = $em->getRepository('ActsCamdramBundle:Person');
        foreach ($people_res->findAll() as $person)
        {
            $surname = $name_utils->extractSurname($person->getName());
            $possibles = $people_res->createQueryBuilder('p')
                ->where('p.name LIKE :name')
                ->andWhere('p.id != :id')
                ->setParameter('name', '% '.$surname)
                ->setParameter('id', $person->getId())
                ->getQuery()->getResult();
            if (count($possibles) > 0) {
                $possible = $name_utils->getMostLikelyUser($person->getName(), $possibles, 85);
                //If the names are identical...assume there are two separate entries for a reason
                if ($possible && $possible->getName() != $person->getName()) {
                    $this->mergeTwoPeople($em, $person, $possible);
                }
            }

        }
        $em->flush();
    }

    public function mergeMapToPeople(EntityManager $em)
    {
        $people_res = $em->getRepository('ActsCamdramBundle:Person');
        foreach ($people_res->findAll() as $person)
        {

            if ($person->getMapTo()) {
                $other = $people_res->findOneById($person->getMapTo());
                $person->setMapTo(null);
                if (!$other) continue;
                $this->mergeTwoPeople($em, $person, $other);
            }
        }
        $em->flush();
    }

    public function mergeTwoPeople(EntityManager $em, Person $p1, Person $p2)
    {
        $count1 = count($p1->getRoles());
        $count2 = count($p2->getRoles());
        if ($count1 < $count2) {
            $temp = $p2;
            $p2 = $p1;
            $p1 = $temp;
        }

        foreach ($p2->getRoles() as $role) {
            $role->setPerson($p1);
        }
        foreach ($p2->getUsers() as $u) {
            $u->setPerson($p1);
        }
        echo "Merged person ".$p2->getName().' into '.$p1->getName()."\r\n";

        $alias = new NameAlias;
        $p1->addAlias($alias);
        $alias->setName($p2->getName());
        $em->persist($alias);

        $em->remove($p2);
        $em->flush();
    }

    /**
     * Auto-create identities based on email address pseudo-guesswork
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    private function generateIdentities(EntityManager $em)
    {
        $users = $em->getRepository('ActsCamdramBundle:User')->findAll();
        foreach ($users as $user) {
            if (preg_match('/^[a-z]+[0-9]+$/i',$user->getEmail())) {
                //Create identity for abc12 (crsid)
                $i = new UserIdentity;
                $i->setService('raven');
                $i->setRemoteUser($user->getEmail());
                $i->setUser($user);
                $user->addIdentity($i);
                $em->persist($i);
            }
            else if (preg_match('/^([a-z]+[0-9]+)@cam\.ac\.uk$/i', $user->getEmail(), $matches)) {
                //Create identity for abc12@cam.ac.uk
                $i = new UserIdentity;
                $i->setService('raven');
                $i->setRemoteUser($matches[1]);
                $i->setUser($user);

                $user->addIdentity($i);
                $em->persist($i);
            }
            else if (preg_match('/^(.*)@cantab.net$/i', $user->getEmail(), $matches)) {
                //Create identity for fred.smith@cantab.net
                $i = new UserIdentity;
                $i->setService('cantab');
                $i->setRemoteUser($matches[1]);
                $i->setUser($user);

                $user->addIdentity($i);
                $em->persist($i);
            }
            else if (preg_match('/^(.*)@(?:gmail|googlemail)\..*$/i', $user->getEmail(), $matches)) {
                //Create identity for xxx@gmail.com / xxx@googlemail.com
                $i = new UserIdentity;
                $i->setService('google');
                $i->setRemoteUser($user->getEmail());
                $i->setUser($user);
                $user->addIdentity($i);
                $em->persist($i);
            }
            else if (preg_match('/^(.*)@(?:hotmail|live|outlook|msn)\..*$/i', $user->getEmail(), $matches)) {
                //Create identity for xxx@hotmail.com etc
                $i = new UserIdentity;
                $i->setService('windows_live');
                $i->setRemoteUser($user->getEmail());
                $i->setUser($user);
                $user->addIdentity($i);
                $em->persist($i);
            }
            else if (preg_match('/^(.*)@(?:yahoo)\..*$/i', $user->getEmail(), $matches)) {
                //Create identity for xxx@yahoo.com etc
                $i = new UserIdentity;
                $i->setService('yahoo');
                $i->setRemoteUser($user->getEmail());
                $i->setUser($user);
                $user->addIdentity($i);
                $em->persist($i);
            }
        }
    }

    private function matchPeopleToUsers(EntityManager $em)
    {
        //Try to map people to users
        $people_res = $em->getRepository('ActsCamdramBundle:Person');
        $users_res = $em->getRepository('ActsCamdramBundle:User');
        $users = $users_res->findAll();

        //First, do exact matches
        foreach ($users as $user) {
            $p = $people_res->findOneByName($user->getName());
            if ($p) {
                $user->setPerson($p);
                echo "Linked ".$p->getName()."\r\n";
            }
        }
        $em->flush();

        //Now do more fuzzy matching based on surnames...
        $users = $em->createQuery('SELECT u FROM ActsCamdramBundle:User u WHERE u.person_id IS NULL')->getResult();

        foreach ($users as $user) {
            preg_match('/(.*) ([a-z\'\-]+)$/i', trim($user->getName()), $matches);
            if (count($matches) == 0) continue;
            $first_names = trim(strtolower($matches[1]));
            $surname = $matches[2];

            $people = $em->createQuery('SELECT p FROM ActsCamdramBundle:Person p WHERE p.name LIKE :name')
                ->setParameter('name', '% '.$surname.'%')->getResult();

            if (count($people) > 0) {
                $min = 99999;
                $p = null;
                foreach ($people as $person) {
                    $person_first_names = str_replace($surname, '', $person->getName());
                    $dist = levenshtein($first_names, $person_first_names);
                    if ($dist < $min) $p = $person;
                }
                $person_first_names = strtolower(trim(str_replace($surname, '', $p->getName())));
                if (!trim($person_first_names) || !trim($first_names)) continue;

                //Split and test sub-names:
                $parts1 = preg_split('/[\s,\-]+/',$first_names);
                $parts2 = preg_split('/[\s,\-]+/',$person_first_names);
                if (count(array_intersect($parts1, $parts2)) > 0) {
                    $user->setPerson($p);
                    echo 'Linked '.$user->getName().' -> '.$p->getName()."\r\n";
                }
                else if ((substr_count($person_first_names, $first_names) > 0 ||  substr_count($first_names, $person_first_names) > 0)) {
                    $user->setPerson($p);
                    echo 'Linked '.$user->getName().' -> '.$p->getName()."\r\n";
                }
                else {
                    similar_text($first_names, $person_first_names, $percent);
                    if (substr($person_first_names, 0, 1) == substr($first_names, 0, 1)) {
                        if ($percent > 55
                            && $first_names != 'mark' && $person_first_names != 'max'
                            && $first_names != 'jon' && $person_first_names != 'joe') {
                            $user->setPerson($p);
                            echo 'Linked '.$user->getName().' -> '.$p->getName()."\r\n";
                        }
                    }
                }
            }
        }

        $em->flush();
    }

    private function createGroupsFromSocieties(EntityManager $em)
    {
        $accesses = $em->getRepository('ActsCamdramBundle:Access')->createQueryBuilder('a')
            ->where('a.type = :type')
            ->setParameter('type', 'society')
            ->getQuery()->getResult();
        ;

        $soc_rep = $em->getRepository('ActsCamdramBundle:Organisation');
        $user_rep = $em->getRepository('ActsCamdramBundle:User');
        $acl = array();

        foreach ($accesses as $access) {
            $soc = $soc_rep->findOneById($access->getRid());
            if (!isset($acl[$soc->getName()])) $acl[$soc->getName()] = array();
            if (!$access->getRevokeId()) $acl[$soc->getName()][] = $access->getUid();
        }

        foreach ($acl as $name => &$users) {
            if (count($users) == 0) unset($acl[$name]);
            sort($users);
        }

        foreach ($acl as $name => &$users) {
            foreach ($acl as $name2 => $users2) {
                if ($name != $name2 && $users == $users2) {
                    echo "De-duplicating $name and $name2\r\n";
                    unset($acl[$name]);
                }
            }
        }

        foreach ($acl as $name => $users) {
            $g = new Group;
            $g->setName($name);
            foreach ($users as $uid) {
                $u = $user_rep->findOneById($uid);
                $g->addUser($u);
            }
            $em->persist($g);
            echo "Created group $name\r\n";
        }
        $em->flush();
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("ALTER TABLE acts_applications DROP FOREIGN KEY FK_95ED3F0FAF648A81");
        $this->addSql("ALTER TABLE acts_events DROP FOREIGN KEY FK_78452C7AAF648A81");
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id)");
        $this->addSql("ALTER TABLE acts_users DROP FOREIGN KEY FK_62A20753217BBB47");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEAF648A81");

        $this->addSql("ALTER TABLE acts_name_aliases DROP FOREIGN KEY FK_355DA778217BBB47");
        $this->addSql("DROP INDEX `IDX_355DA778217BBB47` ON `acts_name_aliases`");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE40A73EBA");
        $this->addSql("ALTER TABLE acts_user_group_links DROP FOREIGN KEY FK_C789B1F6FE54D947");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806EA000B10");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806DF9183C9");
        $this->addSql("ALTER TABLE acl_object_identities DROP FOREIGN KEY FK_9407E54977FA751A");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE2993D9AB4A6");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE299C671CEA1");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B8063D9AB4A6");
        $this->addSql("DROP TABLE acts_name_aliases");
        $this->addSql("DROP TABLE acts_user_identities");
        $this->addSql("DROP TABLE acts_user_group_links");
        $this->addSql("DROP TABLE acts_groups");
        $this->addSql("DROP TABLE acl_classes");
        $this->addSql("DROP TABLE acl_security_identities");
        $this->addSql("DROP TABLE acl_object_identities");
        $this->addSql("DROP TABLE acl_object_identity_ancestors");
        $this->addSql("DROP TABLE acl_entries");
        $this->addSql("ALTER TABLE `acts_events` CHANGE `socid` socid INT NOT NULL");
        $this->addSql("DROP INDEX `IDX_1A1A53FE40A73EBA` ON `acts_shows`");
        $this->addSql("DROP INDEX `IDX_62A20753217BBB47` ON `acts_users`");
        $this->addSql("ALTER TABLE `acts_users` DROP person_id, DROP upgraded_at");
    }

    public function dataDown(ContainerInterface $container)
    {

    }
}