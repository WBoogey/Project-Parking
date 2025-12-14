import { PopiconsChevronBottomLine } from "@popicons/react";
import { cn } from "cn-utility";

interface SelectInputTriggerProps {
  onClick: () => void;
  text: string;
  isOpen: boolean;
  disabled?: boolean;
}

const SelectInputTrigger = ({
  onClick,
  text,
  isOpen,
  disabled,
}: SelectInputTriggerProps) => {
  return (
    <button
      type="button"
      onClick={onClick}
      disabled={disabled}
      data-testid="select-trigger"
      className={cn(
        "flex items-center gap-1 w-full p-2.5 rounded-xl border border-tertiary text-left",
        disabled
          ? "cursor-not-allowed opacity-50 bg-tertiary/20"
          : "cursor-pointer bg-white text-tertiary",
      )}
    >
      <span>{text}</span>
      <PopiconsChevronBottomLine
        color="black"
        className={cn(
          "ml-auto shrink-0 size-6 transition-transform duration-150",
          {
            "-rotate-180": isOpen,
          },
        )}
      />
    </button>
  );
};

export default SelectInputTrigger;
