var supersetCounter = 1;

$(document).ready(function ()
{
    // make the set lap input responsive, and then trigger the input for initial page setup
    $('input.set-lap-count').keyup();

    enableSlidingCards();
});

function enableSlidingCards()
{
    // clicking on a cards header will cause the body and footer to slidetoggle
    $('div.card').each(function (i, exerciseCard)
    {
        $(exerciseCard).find('.card-header').addClass('hover-pointer').off('click').click(function()
        {
            // open the card when clicking the card header
            $(this).siblings().slideToggle('fast');
        }).siblings().toggle(false);

        // stop any inputs, labels, or anything with the class stopToggle inside the header from triggering the slider
        $(exerciseCard).find('.card-header').find('input, label, .stopToggle').click(function(event) { event.stopPropagation(); });
    });
}

function editMuscle(muscleId, muscleName, event)
{
    event.stopPropagation();
    $('form #muscle-id').val(muscleId);
    $('form #muscle-name').val(decodeURIComponent(muscleName)).attr('placeholder', decodeURIComponent(muscleName)).focus();
    $('#save-muscle-card .card-header').siblings().toggle(true);
}

function addMuscleToExercise()
{
    $('#exercise-muscles').append($('#exercise-muscles').children('div').first().clone());
}

function editExercise(exerciseId, exerciseName, exerciseDescription, exerciseRelatedMuscleIds, event)
{
    event.stopPropagation();

    exerciseName = decodeURIComponent(exerciseName);
    exerciseDescription = decodeURIComponent(exerciseDescription);
    exerciseRelatedMuscleIds = JSON.parse(decodeURIComponent(exerciseRelatedMuscleIds));

    $('form #exercise-id').val(exerciseId);
    $('form #exercise-name').val(exerciseName).attr('placeholder', exerciseName).focus();
    $('form #exercise-description').val(exerciseDescription).attr('placeholder', exerciseDescription).focus();
    for (var i = 0; i < exerciseRelatedMuscleIds.length; i++)
    {
        $('#exercise-muscles').children('div').eq(i).find('select').val(exerciseRelatedMuscleIds[i]);
        addMuscleToExercise();
    }
    $('#cancel-exercise-edit-button').removeClass('d-none');

    $('#save-exercise-card .card-header').siblings().toggle(true);
}

function cancelExerciseEdit()
{
    $('form #exercise-id').val('');
    $('form #exercise-name').val('').attr('placeholder', '');
    $('form #exercise-description').val('').attr('placeholder', '');
    addMuscleToExercise();
    $('#exercise-muscles').children('div:not(:last)').remove();
    $('#cancel-exercise-edit-button').addClass('d-none');
}

function gatherExerciseMuscles()
{
    var muscleIds = [];
    $('#exercise-muscles select').each(function (index, select)
    {
        if ($(select).val() !== '')
            muscleIds.push(parseInt($(select).val()));
    });
    $('form #related-muscles').val(JSON.stringify(muscleIds));
}

function gatherWorkoutSets()
{
    var sets = [];
    $('#workout-sets .card').each(function (i, card)
    {
        var set = {
            'exercise-id-numbers': [],
            'superset': $(card).find('input[type=checkbox]').is(':checked')
        };
        $(card).find('select').each(function (j, select)
        {
            if ($(select).val() != '')
            set['exercise-id-numbers'].push($(select).val());
        });
        sets.push(set);
    });
    $('#workout-form #sets').val(JSON.stringify(sets));
    return true;
}

function changeNumberOfSetLaps(input)
{
    // find the set card to use in other places going forward
    var card = $(input).parentsUntil('.card').last().parent();

    // make the superset input responsive
    $(card).find('input.superset-input').off('change').change(function ()
    {
        // change the colour of the card header depending on if the set is a superset
        var cardHeader = $(this).parentsUntil('.card-header').last().parent();
        if ($(this).is(':checked'))
            $(cardHeader).addClass('bg-warning').removeClass('bg-dark text-white');
        else
            $(cardHeader).removeClass('bg-warning').addClass('bg-dark text-white');
    });

    // if newRounds is blank, then we assume that the user is going to enter a new numer of rounds and therefore we do nothing in anticipation of that.
    // if the user wants to do 0 rounds, they should enter 0
    var newRounds = $(input).val();
    if (newRounds > 0)
    {
        do
        {
            var currentSetRounds = $(card).find('.card-body').children().first().find('.set-lap:not(:first)').length

            $(card).find('.card-body').children().each(function (rowIndex, exerciseRow)
            {
                // adding a round
                if (currentSetRounds < newRounds)
                    $(exerciseRow).find('.set-lap').last().after($(exerciseRow).find('.set-lap').last().clone(true));
                else if (currentSetRounds > newRounds)
                    $(exerciseRow).find('.set-lap').last().remove();
            });

        } while (currentSetRounds != newRounds)

        // show all set-rounds, but not the first of each exercise
        $(card).find('.card-body').children().each(function (exerciseIndex, exerciseRow)
        {
            $(exerciseRow).find('.set-lap:not(:first)').removeClass('d-none');
        });

        // rename all inputs so that each is unique
        var setIndex = $(card).index('.card');

        var supersetUniqueText = 'workout['+setIndex+'][is-superset]';
        $(card).find('.superset-input').attr('name', supersetUniqueText);
        $(card).find('.superset-input').attr('id', supersetUniqueText);
        $(card).find('.superset-input').siblings('label').attr('for', supersetUniqueText);

        $(card).find('.card-body').children().each(function (exerciseIndex, exerciseRow)
        {
            $(exerciseRow).find('input, select').each(function (inputIndex, input)
            {
                var uniqueText = 'workout['+setIndex+']['+exerciseIndex+']['+$(input).data()['name']+'][]';

                if ($(input).is(':checkbox'))
                {
                    $(input).attr('id', uniqueText);
                    $(input).siblings('label').attr('for', uniqueText);
                    $(input).attr('name', uniqueText);
                }

                if ($(input).is(':visible'))
                    $(input).attr('name', uniqueText);
            });
        });
    }
}

function addExerciseToSet(addButtonClicked)
{
    // get the card to use later
    var setCard = $(addButtonClicked).parentsUntil('.card').last().parent();
    // duplicate the last exercise
    $(setCard).find('.card-body').append($(setCard).find('.card-body').children().last().clone(false));
    // clear the last exercises inputs
    $(setCard).find('.card-body').children().last().find('input').val('');
    changeNumberOfSetLaps($(setCard).find('.card-header .set-lap-count'));
}

function addSetToWorkout()
{
    // duplicate the last set card
    $('body form .card:last').after($('body form .card').last().clone(false));

    // clear all inputs on the new set
    $('body form .card:last .card-body input').val('');

    // set the set lap count to 1
    $('body form .card:last .card-header .set-lap-count').val(1);

    // remove all exercises from the last set, changing teh number of set laps later will bring back the first exercise.
    $('body form .card:last .card-body').children().first().siblings().remove();

    // on every card,
    $('body form .card').each(function (i, setCard)
    {
        // update the set number
        $(setCard).find('.card-header .set-index').html(i+1);

        // refresh the set laps and all input ids and names
        changeNumberOfSetLaps($(setCard).find('.card-header .set-lap-count'));
    });

    enableSlidingCards();
}
