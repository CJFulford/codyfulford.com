var supersetCounter = 1;

$(document).ready(function ()
{
    // make the set lap input responsive, and then trigger the input for initial page setup
    $('input.set-lap-count').keyup();

    enableSlidingCards();

    loadWorkout();
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
        var lapCountUniqueText = 'workout['+setIndex+'][lap-count]';
        $(card).find('.set-lap-count').attr('name', lapCountUniqueText);
        $(card).find('.superset-input').attr('name', supersetUniqueText);
        $(card).find('.superset-input').attr('id', supersetUniqueText);
        $(card).find('.superset-input').siblings('label').attr('for', supersetUniqueText);

        $(card).find('.card-body').children().each(function (exerciseIndex, exerciseRow)
        {
            $(exerciseRow).find('.set-lap:not(.d-none) input, select').each(function (inputIndex, input)
            {
                var uniqueText = 'workout['+setIndex+'][exercises]['+exerciseIndex+']['+$(input).data()['name']+'][]';

                if ($(input).is(':checkbox'))
                {
                    $(input).attr('id', uniqueText);
                    $(input).siblings('label').attr('for', uniqueText);
                }

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
    $('body form .card:last').after($('body form .card').last().clone(true));

    // clear all inputs on the new set
    $('body form .card:last .card-body input').val('');

    // set superset to false
    if ($('body form .card:last .superset-input').is(':checked'))
        $('body form .card:last .superset-input').click();

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

function loadWorkout()
{
    if ($('#loaded-workout-details').length > 0)
    {
        // load the workout details from the encoded json string in the html
        var workout = JSON.parse(decodeURIComponent($('#loaded-workout-details').html()));

        // go over each set.
        $(workout.sets).each(function (setIndex, set)
        {
            // add enough sets to thw workout page
            // we skip index 0 because teh page loads by default with a set
            if (setIndex > 0)
                $('#add-workout-set-button').click();

            // click the superset box if appropriate
            if (set.is_superset)
                $('.card:last .superset-input').click();

            // fill in the lap count input
            $('.card:last .set-lap-count').val(set.lap_count).trigger('keyup');

            //add on as many exercises as needed
            for (var i = 0; i < set.exercise_id_numbers.length - 1; i++)
                $('.card:last .add-set-exercise-button').click();

            // populate the exercise selects
            $('.card:last .card-body select').each(function (index, select) { $(select).val(set.exercise_id_numbers[index]); });

            // populate the weight values
            $('.card:last .card-body .set-lap:not(.d-none) input[data-name=weight]').each(function (index, input)
            {
                $(input).attr('placeholder', 'Weight ('+set.exercises[index]['weight'].toFixed(1)+')');
                $(input).val(set.exercises[index]['weight'].toFixed(1));
            });
            // populate the repetition values
            $('.card:last .card-body .set-lap:not(.d-none) input[data-name=repetitions]').each(function (index, input)
            {
                $(input).attr('placeholder', 'Repetitions ('+set.exercises[index]['repetitions']+')');
                $(input).val(set.exercises[index]['repetitions']);
            });

            // refresh the set laps and all input ids and names
            //changeNumberOfSetLaps($('.card:last .card-header .set-lap-count'));
        });
    }
}
