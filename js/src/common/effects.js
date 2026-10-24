export function effects(min, max, result, config) {
  if (!Number.isInteger(result) || result < min || result > max) return [];
  const matches = [];
  const faces = max - min + 1;
  if (config.criticalEnabled && faces >= config.criticalMinFaces) {
    const band = Math.min(Math.floor(faces / 2), Math.max(1, Math.floor(faces * config.criticalPercent / 100)));
    if (result >= max - band + 1) matches.push(config.successHigh ? 'success' : 'failure');
    if (result <= min + band - 1) matches.push(config.successHigh ? 'failure' : 'success');
  }
  if (config.signEnabled && min < 0 && max > 0) matches.push(result > 0 ? 'positive' : result < 0 ? 'negative' : 'zero');
  const digits = String(Math.abs(result));
  if (digits.length >= config.digitsMin) {
    if (config.repeatEnabled && /^(\d)\1+$/.test(digits)) matches.push('repeat');
    const differences = [...digits].slice(1).map((d, i) => Number(d) - Number(digits[i]));
    if (config.sequenceEnabled && (differences.every(d => d === 1) || differences.every(d => d === -1))) matches.push('sequence');
  }
  if (config.specialEnabled && config.specialNumbers.includes(result)) matches.push('special');
  return config.priority.filter(effect => matches.includes(effect));
}
export function iconType(min, max) {
  return min === 1 && [4,6,8,10,12,20,100].includes(max) ? 'd' + max : 'generic';
}
export function cssVariables(config, effect) {
  const vars = {};
  for (const theme of ['light','dark']) {
    for (const property of ['text','background','icon']) {
      const value = config.colors[effect]?.[theme]?.[property]
        || (property === 'icon' ? '' : config.colors.normal[theme][property]);
      if (value && /^#(?:[a-f\d]{3}|[a-f\d]{4}|[a-f\d]{6}|[a-f\d]{8})$/i.test(value)) vars['--edge-dice-' + property + '-' + theme] = value;
    }
  }
  return vars;
}
