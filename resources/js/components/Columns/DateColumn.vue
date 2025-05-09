<template>
  <div>
    <template v-if="isValidDate">
      {{ formattedDate }}
    </template>
    <span v-else class="text-gray-400 italic">
      {{ column.default || '—' }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import get from 'lodash/get';
import moment from 'moment';

const props = defineProps({
  column: {
    type: Object,
    required: true,
  },
  record: {
    type: Object,
    required: true,
  },
});

const rawValue = computed(() => get(props.record, props.column.name));

const dateObject = computed(() => {
  if (!rawValue.value) return null;

  const date = moment(rawValue.value);

  // Check if date is valid
  if (!date.isValid()) return null;

  return date;
});

const isValidDate = computed(() => dateObject.value !== null);

// Function to convert PHP date format to Moment.js format
const phpToMomentFormat = (phpFormat) => {
  if (!phpFormat) return null;

  // PHP to Moment.js format mapping
  const formatMap = {
    // Day
    'd': 'DD',     // Day of the month, 2 digits with leading zeros
    'D': 'ddd',    // A textual representation of a day, three letters
    'j': 'D',      // Day of the month without leading zeros
    'l': 'dddd',   // A full textual representation of the day of the week
    'N': 'E',      // ISO-8601 numeric representation of the day of the week
    'S': 'Do',     // English ordinal suffix for the day of the month, 2 characters
    'w': 'd',      // Numeric representation of the day of the week
    'z': 'DDD',    // The day of the year (starting from 0)

    // Week
    'W': 'W',      // ISO-8601 week number of year, weeks starting on Monday

    // Month
    'F': 'MMMM',   // A full textual representation of a month, such as January or March
    'm': 'MM',     // Numeric representation of a month, with leading zeros
    'M': 'MMM',    // A short textual representation of a month, three letters
    'n': 'M',      // Numeric representation of a month, without leading zeros
    't': '',       // Number of days in the given month

    // Year
    'L': '',       // Whether it's a leap year
    'o': 'GGGG',   // ISO-8601 week-numbering year
    'Y': 'YYYY',   // A full numeric representation of a year, 4 digits
    'y': 'YY',     // A two digit representation of a year

    // Time
    'a': 'a',      // Lowercase Ante meridiem and Post meridiem
    'A': 'A',      // Uppercase Ante meridiem and Post meridiem
    'B': '',       // Swatch Internet time
    'g': 'h',      // 12-hour format of an hour without leading zeros
    'G': 'H',      // 24-hour format of an hour without leading zeros
    'h': 'hh',     // 12-hour format of an hour with leading zeros
    'H': 'HH',     // 24-hour format of an hour with leading zeros
    'i': 'mm',     // Minutes with leading zeros
    's': 'ss',     // Seconds with leading zeros
    'u': 'SSSSSS', // Microseconds
    'v': 'SSS',    // Milliseconds

    // Timezone
    'e': 'z',      // Timezone identifier
    'I': '',       // Whether or not the date is in daylight saving time
    'O': 'ZZ',     // Difference to Greenwich time (GMT) without colon between hours and minutes
    'P': 'Z',      // Difference to Greenwich time (GMT) with colon between hours and minutes
    'T': 'z',      // Timezone abbreviation
    'Z': '',       // Timezone offset in seconds

    // Full Date/Time
    'c': 'YYYY-MM-DDTHH:mm:ssZ', // ISO 8601 date
    'r': 'ddd, DD MMM YYYY HH:mm:ss ZZ', // RFC 2822 formatted date
    'U': 'X'      // Seconds since the Unix Epoch
  };

  let momentFormat = '';

  // Loop through each character in the PHP format string
  for (let i = 0; i < phpFormat.length; i++) {
    const char = phpFormat[i];

    // Check if this is an escaped character
    if (char === '\\' && i + 1 < phpFormat.length) {
      momentFormat += '[' + phpFormat[i + 1] + ']';
      i++; // Skip the next character as it's escaped
    }
    // Check if it's a format character
    else if (formatMap[char] !== undefined) {
      momentFormat += formatMap[char] || char;
    }
    // Otherwise, escape the character to avoid Moment.js interpreting it
    else {
      momentFormat += '[' + char + ']';
    }
  }

  return momentFormat;
};

const formattedDate = computed(() => {
  if (!isValidDate.value) return '';

  const date = dateObject.value;

  // Apply timezone if specified
  if (props.column.timezone) {
    date.tz(props.column.timezone);
  }

  // Apply locale if specified
  if (props.column.locale) {
    date.locale(props.column.locale);
  }

  // Convert PHP format to Moment.js format
  const format = phpToMomentFormat(props.column.format);

  // Format the date
  if (format) {
    return date.format(format);
  }

  // Default format if none specified
  return date.format('YYYY-MM-DD HH:mm:ss');
});
</script>