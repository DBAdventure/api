<?php

namespace Dba\GameBundle\Event;

final class DbaEvents
{
    const BEFORE_MOVE = 'event.before.move';
    const AFTER_MOVE = 'event.after.move';
    const BEFORE_ATTACK = 'event.before.attack';
    const AFTER_ATTACK = 'event.after.attack';
    const BEFORE_SPELL = 'event.before.spell';
    const AFTER_SPELL = 'event.after.spell';
    const BEFORE_STEAL = 'event.before.steal';
    const AFTER_STEAL = 'event.after.steal';
    const BEFORE_SLAP = 'event.before.slap';
    const AFTER_SLAP = 'event.after.slap';
    const BEFORE_HEAL = 'event.before.heal';
    const AFTER_HEAL = 'event.after.heal';
    const BEFORE_ANALYSIS = 'event.before.analysis';
    const AFTER_ANALYSIS = 'event.after.analysis';
}
