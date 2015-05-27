<?php

namespace OpenCFP\Application;

use OpenCFP\Domain\CallForProposal;
use OpenCFP\Domain\Entity\Talk;
use OpenCFP\Domain\Services\EventDispatcher;
use OpenCFP\Domain\Services\IdentityProvider;
use OpenCFP\Domain\Speaker\SpeakerProfile;
use OpenCFP\Domain\Speaker\SpeakerRepository;
use OpenCFP\Domain\Talk\TalkRepository;
use OpenCFP\Domain\Talk\TalkSubmission;
use OpenCFP\Domain\Talk\TalkWasSubmitted;

class Speakers
{

    /** @var CallForProposal */
    protected $callForProposal;

    /** @var IdentityProvider */
    protected $identityProvider;

    /** @var SpeakerRepository */
    protected $speakers;

    /** @var TalkRepository */
    protected $talks;

    /** @var EventDispatcher */
    private $dispatcher;

    public function __construct(
        CallForProposal $callForProposal,
        IdentityProvider $identityProvider,
        SpeakerRepository $speakers,
        TalkRepository $talks,
        EventDispatcher $dispatcher
    ) {
        $this->speakers = $speakers;
        $this->identityProvider = $identityProvider;
        $this->talks = $talks;
        $this->callForProposal = $callForProposal;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Retrieves the speaker profile for currently authenticated speaker.
     *d
     * @return SpeakerProfile
     */
    public function findProfile()
    {
        $speaker = $this->identityProvider->getCurrentUser();

        return new SpeakerProfile($speaker);
    }

    /**
     * Retrieves a talk owned by a speaker.
     *
     * @param $talkId
     *
     * @return Talk
     * @throws NotAuthorizedException
     */
    public function getTalk($talkId)
    {
        $speaker = $this->identityProvider->getCurrentUser();
        $talk = $speaker->talks->where(['id' => $talkId])->execute()->first();

        // If it can't grab by relation, it's likely not their talk.
        if (!$talk) {
            throw new NotAuthorizedException;
        }

        // Do an explicit check of ownership because why not.
        if ($talk->user_id !== $speaker->id) {
            throw new NotAuthorizedException;
        }

        return $talk;
    }

    /**
     *
     */
    public function getTalks()
    {
        $speaker = $this->identityProvider->getCurrentUser();

        return $speaker->talks->execute();
    }

    /**
     * Orchestrates the use-case of a speaker submitting a talk.
     *
     * @param TalkSubmission $submission
     *
     * @return Talk
     * @throws \Exception
     */
    public function submitTalk(TalkSubmission $submission)
    {
        if (!$this->callForProposal->isOpen()) {
            throw new \Exception('You cannot create talks once the call for papers has ended.');
        }

        $user = $this->identityProvider->getCurrentUser();

        // Create talk from submission.
        $talk = $submission->toTalk();

        // Own the talk to the speaker.
        $talk->user_id = $user->id;

        $this->talks->persist($talk);

        $this->dispatcher->dispatch('opencfp.talk.submit', new TalkWasSubmitted($talk));

        return $talk;
    }
}
