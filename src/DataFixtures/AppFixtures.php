<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Contact;
use App\Entity\Event;
use App\Entity\Member;
use App\Entity\Notification;
use App\Entity\Participant;
use App\Entity\Publication;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // factory to create a Faker\Generator instance
        $faker = Factory::create('fr_FR');

        //  Start Users Fixtures
        for ($usr = 0; $usr < 15; $usr++) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setPrenom($faker->firstName);
            $user->setNom($faker->lastName);
            $user->setIsVerified(true);
            $user->setCreatedAt(new DateTimeImmutable("now"));
            $user->setUpdatedAt(new DateTimeImmutable("now"));
            $user->setDescription($faker->sentence);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'secret')
            );
            $user->setCountry($faker->country);
            $user->setAnnif($faker->dateTimeThisCentury());
            $user->setCity($faker->city);
            $this->addReference('usr-' . $usr, $user);
            $manager->persist($user);
        }
        // End Users Fixtures

        // Start Contacts Fixtures
        for ($contacts = 0; $contacts < 20; $contacts++) {
            $contact = new Contact;
            $user1 = $this->getReference('usr-' . rand(0, 9));
            $user2 = $this->getReference('usr-' . rand(0, 9));

            while ($user1 === $user2) {
                $user2 = $user1 === $user2 ? $this->getReference('usr-' . rand(0, 9)) : $user2;
            }

            $contact->setUser($user1);
            $contact->setUsername($user2->getUsername());
            $contact->setContactUsername($user1->getUserIdentifier());
            $manager->persist($contact);
        }
        // End Contacts Fixtures

        // Start Events Fixtures 
        for ($events = 0; $events < 5; $events++) {
            $event = new Event;
            $user = $this->getReference('usr-' . rand(0, 8));
            $event->setName($faker->word());
            $event->setUser($user);
            $event->setDescription($faker->sentence);
            $event->setTag($faker->word);

            $this->addReference('event-' . $events, $event);

            $manager->persist($event);
        }
        // End Events Fixtures

        // Start Events Fixtures
        for ($members = 0; $members < 20; $members++) {
            $member = new Member;
            $user1 = $this->getReference('usr-' . rand(0, 9));
            $user2 = $this->getReference('usr-' . rand(0, 9));

            while ($user1 === $user2) {
                $user2 = $user1 === $user2 ? $this->getReference('usr-' . rand(0, 9)) : $user2;
            }

            $member->setUsername($user1->getUsername());
            $member->setUser($user1);
            $member->setLastMessage($faker->sentence());
            $member->setOtherUsername($user2->getUsername());

            $manager->persist($member);
        }
        // End Events Fixtures

        // Start Members Fixtures
        for ($members = 0; $members < 20; $members++) {
            $member = new Member;
            $user1 = $this->getReference('usr-' . rand(0, 9));
            $user2 = $this->getReference('usr-' . rand(0, 9));

            while ($user1 === $user2) {
                $user2 = $user1 === $user2 ? $this->getReference('usr-' . rand(0, 9)) : $user2;
            }

            $member->setUsername($user1->getUsername());
            $member->setUser($user1);
            $member->setLastMessage($faker->sentence());
            $member->setOtherUsername($user2->getUsername());

            $manager->persist($member);
        }
        // End Member Fixtures

        // Notifs

        for ($notifications = 0; $notifications < 20; $notifications++) {
            $notification = new Notification;
            $user = $this->getReference('usr-' . rand(0, 9));

            $notification->setUser($user);
            $notification->setCategorie($user->getUsername());
            $notification->setName($faker->word());

            $manager->persist($notification);
        }

        // Notifs

        // Participants
        for ($participants = 0; $participants < 20; $participants++) {
            $participant = new Participant;
            $event = $this->getReference('event-' . rand(0, 4));
            $user = $this->getReference('usr-' . rand(0, 9));

            $participant->setEvent($event);
            $participant->setEventName($event->getName());
            $participant->setParticipantUsername($user->getUsername());
            $participant->setUserId($user->getId());
            $manager->persist($participant);
        }
        // Participants

        // Publications

        for ($publications = 0; $publications < 20; $publications++) {
            $publication = new Publication;
            $user = $this->getReference('usr-' . rand(0, 9));

            $publication->setPublisher($user->getUsername());
            $publication->setContent($faker->text());

            $manager->persist($publication);
        }

        // Publications

        $manager->flush();
    }
}
