import { PopiconsChevronBottomLine } from "@popicons/react";
import { cn } from "cn-utility";
import { useState, useRef, useEffect } from "react";
import DropdownList from "./DropdownList";
import type {
  DropdownFullListType,
  DropdownListType,
} from "@/types/LinkDropdownTypes";

interface LinkDropdownProps {
  title: string;
  dropdownElements: DropdownFullListType;
  className?: string;
}

const LinkDropdown = ({
  title,
  className,
  dropdownElements,
}: LinkDropdownProps) => {
  const [open, setOpen] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (
        dropdownRef.current &&
        !dropdownRef.current.contains(event.target as Node)
      ) {
        setOpen(false);
      }
    };

    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  return (
    <div ref={dropdownRef} className={cn("relative", className)}>
      <button
        className="flex items-center gap-1 p-1 cursor-pointer text-secondary hover:underline"
        onClick={() => setOpen(!open)}
      >
        {title}
        <PopiconsChevronBottomLine />
      </button>
      <div
        className={cn(
          "absolute min-w-52 left-1/3 px-3.5 py-3 rounded-xl bg-primary border border-black/50 overflow-hidden transition-all duration-75 flex flex-col gap-2",
          open
            ? "max-h-96 opacity-100 top-7/8"
            : "max-h-0 opacity-0 pointer-events-none top-1/2",
        )}
      >
        {dropdownElements.map((list: DropdownListType, index: number) => {
          const isLast = index === dropdownElements.length - 1;
          return (
            <>
              <DropdownList key={index} list={list} />
              {!isLast && <span className="block bg-black/25 h-px w-full" />}
            </>
          );
        })}
      </div>
    </div>
  );
};

export default LinkDropdown;
