import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import LinkDropdown from "./LinkDropdown";
import userEvent from "@testing-library/user-event";

const mockDropdownElements = [
  [
    { title: "Item 1", href: "/item1" },
    { title: "Item 2", href: "/item2" },
  ],
  [{ title: "Item 3", href: "/item3" }],
  [
    { title: "Item 4", href: "/item4" },
    { title: "Item 5", href: "/item5" },
  ],
];

describe("LinkDropdown", () => {
  it("should render with default parameters", () => {
    // Arrange
    const { container } = render(
      <LinkDropdown title="Menu" dropdownElements={mockDropdownElements} />,
    );
    const popover = container.querySelector(".absolute");

    // Assert
    expect(screen.getByText("Menu")).toBeInTheDocument();
    expect(popover).toBeInTheDocument();
    expect(popover).toHaveClass("max-h-0 opacity-0 pointer-events-none");
  });

  it("should toggle on click", async () => {
    // Assert
    const { container } = render(
      <LinkDropdown title="Menu" dropdownElements={mockDropdownElements} />,
    );
    const user = userEvent.setup();
    const button = screen.getByRole("button");

    // Act & Assert 1
    await user.click(button);

    const openDropdown = container.querySelector(".absolute");
    expect(openDropdown).toHaveClass("opacity-100");

    // Act & Assert 2
    await user.click(button);
    const closedDropdown = container.querySelector(".absolute");
    expect(closedDropdown).toHaveClass("max-h-0 opacity-0 pointer-events-none");
  });

  it("should render all list items in dropdown", () => {
    // Arrange
    render(
      <LinkDropdown title="Menu" dropdownElements={mockDropdownElements} />,
    );
    const allItems = mockDropdownElements.flat();

    // Assert
    allItems.forEach((item) => {
      const element = screen.getByText(item.title);
      expect(element).toBeInTheDocument();
      expect(element.closest("a")).toHaveAttribute("href", item.href);
    });
  });

  it("should have a separator between each link list", () => {
    // Arrange
    const { container } = render(
      <LinkDropdown title="Menu" dropdownElements={mockDropdownElements} />,
    );
    const dividers = container.querySelectorAll("span.block.h-px");

    // Assert
    expect(dividers).toHaveLength(mockDropdownElements.length - 1);
  });
});
