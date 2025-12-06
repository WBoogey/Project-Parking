interface SelectInputChoiceProps {
  choice: string;
  onSelect: () => void;
}

const SelectInputChoice = ({ choice, onSelect }: SelectInputChoiceProps) => {
  return (
    <button
      type="button"
      onClick={onSelect}
      data-testid={`select-choice-${choice}`}
      className="w-full cursor-pointer text-secondary font-semibold text-left py-0.5 first:pt-0 last:pb-0"
    >
      {choice}
    </button>
  );
};

export default SelectInputChoice;

