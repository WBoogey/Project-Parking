import { PopiconsChevronBottomLine } from "@popicons/react";
import { cn } from "cn-utility";

interface SelectInputTriggerProps {
  onClick: () => void;
  text: string;
  isOpen: boolean;
}

const SelectInputTrigger = ({
  onClick,
  text,
  isOpen,
}: SelectInputTriggerProps) => {
  return (
    <button
      type="button"
      onClick={onClick}
      data-testid="select-trigger"
      className="flex items-center gap-1 cursor-pointer text-tertiary font-semibold bg-white w-full p-2.5 rounded-xl border border-tertiary text-left"
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
